# This script inputs all files got from the web and outputs all the tables for the MySQL database


# PROGRAM IMPLEMENTATION:

DataDir = "../Data/"

# Define imput files

import os

#### HUMAN ####

# #ID mapping file:
# os.system("awk -v FS='\\t' -v OFS='\\t' '{print $2,$3,$19}' "+DataDir+"HUMAN_9606_idmapping_selected.tab >" +DataDir+"HUMAN_9606_idmapping_selected_simple.tab")

# #Gene info file
# os.system("awk -v FS='\\t' -v OFS='\\t' '{print $1,$2,$3,$5,$6}' "+DataDir+"Homo_sapiens.gene_info >" +DataDir+"Homo_sapiens_simple.gene_info")

# # define input files:

#IDmap_filename = DataDir+"HUMAN_9606_idmapping_selected_simple.tab"
#Sprot_filename = DataDir+"uniprot_sprot_human.dat"
#Gene_info_filename = DataDir+"Homo_sapiens_simple.gene_info"

#### ALL ####

#ID mapping file:
#os.system("awk -v FS='\\t' -v OFS='\\t' '{print $2,$3,$19}' "+DataDir+"idmapping_selected.tab >" +DataDir+"idmapping_selected_simple.tab")

print("ID mapping parsed with awk")

#Gene info file
#os.system("awk -v FS='\\t' -v OFS='\\t' '{print $1,$2,$3,$5,$6}' "+DataDir+"gene_info >" +DataDir+"gene_info_simple")

print("Gene info parsed with awk")

# define input files:

IDmap_filename = DataDir+"idmapping_selected_simple.tab"
Sprot_filename = DataDir+"uniprot_sprot.dat"
Gene_info_filename = DataDir+"gene_info_simple"


# common input files

Tax_names_filename = DataDir+"taxdmp/names.dmp"
Tax_nodes_filename = DataDir+"taxdmp/nodes.dmp"
Division_filename = DataDir+"taxdmp/division.dmp"
OrthoDB_OG2genes_filename = DataDir+"OrthoDB/odb9v1_OG2genes.tab"
OrthoDB_genes_filename = DataDir+"OrthoDB/odb9v1_genes.tab"
OrthoDB_OGnames_filename = DataDir+"OrthoDB/odb9v1_OGs.tab"

##### DEFINE TAXIDS #####


# corrected set:

# Until Pan troglodytes

#TaxIDs = set(["9606","10090","9913","9598","9031","8355","7955","4097","559292", "284812","7227","6239","83333", "224308"])

TaxIDs = set(["9606", # Homo sapiens
	"10090","947985","35531","116058","10091","10092","1385377","80274","1643390","179238","1879032","57486","39442", #Mus musculus
	"9913", #Bos taurus 
	"9598", "756884", "37010", "37011", "91950", "37012", "1294088",  #Pan troglodytes
	"9031", "208525", "405000", "400035", "208524",  #Gallus gallus
	"8355", "443947", "288554", # Xenopus laevis
	"7955", # Danio Rerio
	"4097", # Nicotiana tabaccum
	"4932", "1337652", "1247190", "1196866", "559292", "1418121",	# Saccharomyces cerevisiae
	"4896", "1264690", "284812", "614666", "614667", "614668", "941278", "649908", "941279", "881122",  # Schizosaccharomyces pombe
	"7227", # Drosophila melanogaster
	"6239",  # Caenorhabditis elegans
	"562", "866768", "498388", "1409786" # Escherichia coli, 
	"1423", "1340494", "935855"]) # Bacillus subtilis
	

##### PARSE STUFF #######

# Parse the Gene_info_filename and record into a dictionary (Key: NCBIid) a list that has [RecName, TaxID, [Synonimous]]

GeneInfo_map = {}

with open(Gene_info_filename,"r") as fd:
	for line in fd:
		
		content = line.strip().split("\t")
		TaxID = content[0].strip()

		if TaxID in TaxIDs:

			NCBIid = content[1].strip()
			RecName = content[2].strip()
			Synonimous = list(set(content[3].upper().split("|")))
			if len(Synonimous)<2:
				Synonimous = ["-"]
			GeneInfo_map[NCBIid] = [RecName, TaxID, Synonimous]

GeneInfo_map_keys = GeneInfo_map.keys()

print("Gene info parsed")

# Load ID mapping into dictionary of Tupples (key: UniProt_ID, Values: (NCBI_ID , ENSEMBLid)):

IDmap = {}
with open(IDmap_filename,"r") as fd:
	for line in fd:
		content = line.strip("\n").split("\t")
		NCBI_ID = content[1].split(";")[0].strip()
 
		if NCBI_ID in GeneInfo_map_keys:

			Prot_ID = content[0].strip()
			ENSEMBLid = content[2].split(";")[0].strip()

			IDmap[Prot_ID] = (NCBI_ID,ENSEMBLid)

IDmap_keys = IDmap.keys()

print("ID mapping parsed")

# delete genes that don't have a protein entry in UniProt:


# import pickle

# # obj0, obj1, obj2 are created here...

# # Saving the objects:
# with open('objs.pkl', 'wb') as f:  # Python 3: open(..., 'wb')
#     pickle.dump([obj0, obj1, obj2], f)

# # Getting back the objects:
# with open('objs.pkl','rb') as f:  # Python 3: open(..., 'rb')
#     obj0, obj1, obj2 = pickle.load(f)


# Parse the Sprot_filename and add to dictionary that cointains UniProt_ID as key and a list as values: (NCBI_geneID , Ensembl_geneID, RecName, AltName(s), Short Name (s), PFAM_domain(s), PFAM_domain(s) name(s),sequence, GO ID (s), GO type(s), GO (name)), [GOsC],[GOsF],[GOsP][SimilarGOsC], [SimilarGOsF], [SimilarGOsP]

CDS_Genes = set()
UniProt_map = {}	
seq = ""
writing_seq = 0

with open(Sprot_filename,"r") as fd:

	prev_line = ""
	for line in fd:

		# start a new entry, adding gene_IDs:
		if line.startswith("ID"):

			ID = line.split()[1].strip()

			if ID in IDmap_keys:
				NCBIid = IDmap[ID][0]
				UniProt_map[ID] = [NCBIid,IDmap[ID][1],'-',["-"],["-"],["-"],["-"],'-',["-"],["-"],["-"],["-"],["-"],["-"],["-"],["-"],["-"]]
				
				# record all the genes that have a protein:
				CDS_Genes.add(NCBIid)

			
			# skip the first line
			else:
				NCBIid = ""
				continue

		else:

			#skip other lines
			if NCBIid not in GeneInfo_map_keys:
				continue

		# add names:
		if line.startswith("DE"):

			# RecName:
			if line.split()[1]=="RecName:" and prev_line.split()[1].startswith("Contains") is False:
				RecName_info = line.split()[2:]
				if RecName_info[-1].startswith('{') is True:
					RecName_info.pop()
				RecName = " ".join(RecName_info).split("=")[1].split(";")[0]
				UniProt_map[ID][2] = RecName

			# AltNames(s), in the form of a list that will be joined below:
			if line.split()[1]=="AltName:":
				AltName_info = line.split()[2:]
				if AltName_info[-1].startswith('{') is True:
					AltName_info.pop()
				AltName = " ".join(AltName_info).split("=")[1].split(";")[0].upper()
				
				if AltName not in UniProt_map[ID][3]:
					UniProt_map[ID][3].append(AltName)

			# ShortName(s), in the form of a list that will be joined below:
			if line.split()[1].startswith("Short="):
				ShortName_info = line.split()[1:]
				if ShortName_info[-1].startswith('{') is True:
					ShortName_info.pop()
				ShortName = " ".join(ShortName_info).split("=")[1].split(";")[0].upper()
				
				if ShortName not in UniProt_map[ID][4]:
					UniProt_map[ID][4].append(ShortName)

		# add Pfam domain IDs and names:
		if line.startswith("DR") and line.split()[1]=="Pfam;":
			Pfam_ID = line.split()[2].split(";")[0].strip()
			UniProt_map[ID][5].append(Pfam_ID)

			# add name
			Pfam_name = line.split()[3].split(";")[0].strip()
			UniProt_map[ID][6].append(Pfam_name)

		# add aa sequence:

		# if prev_line.startswith("SQ") is True:
		# 	writing_seq = 1

		# if line.startswith("//") is True:
		# 	UniProt_map[ID][7] = seq
		# 	seq = ""
		# 	writing_seq = 0

		# if writing_seq == 1:
		# 	seq += "".join(line.strip().split())


		# add GO information:

		if line.startswith("DR") and line.split()[1]=="GO;":

			GOid = line.split(":")[1].split(";")[0]
			Type_GO = line.split()[3].split(":")[0]
			Name_GO = line.split(":")[2].split(";")[0]

			UniProt_map[ID][8].append(GOid)
			UniProt_map[ID][9].append(Type_GO)
			UniProt_map[ID][10].append(Name_GO)

			if Type_GO=="C":
				UniProt_map[ID][11].append(GOid)
			if Type_GO=="F":
				UniProt_map[ID][12].append(GOid)
			if Type_GO=="P":
				UniProt_map[ID][13].append(GOid)								

		# record the current line for the next run
		prev_line = line

Uniprot_map_keys = UniProt_map.keys()


		
print("Sprot parsed, it has "+str(len(UniProt_map.keys()))+" Entries")

GeneInfo_map_keys = set(GeneInfo_map_keys)

#print (CDS_Genes)
# delete genes that don't have protein:

for NCBIid in GeneInfo_map_keys:

	if NCBIid not in CDS_Genes:

		GeneInfo_map.pop(NCBIid)


print ("Taken all genes that have a protein")

# add to each protein the UniprotIDs of those with similar GOs:

MaxN = 10

i = 1

for IDq in Uniprot_map_keys:

	print("Looking at the UniprotID number: "+str(i))
	i += 1

	C_q = set(UniProt_map[IDq][11])
	F_q = set(UniProt_map[IDq][12])
	P_q = set(UniProt_map[IDq][13])

	TaxID_q = GeneInfo_map[UniProt_map[IDq][0]][1]

	Nsimilar_foundC = 0
	Nsimilar_foundF = 0
	Nsimilar_foundP = 0

	for IDs in Uniprot_map_keys:

		TaxID_s = GeneInfo_map[UniProt_map[IDs][0]][1]

		if TaxID_s == TaxID_q and IDq != IDs and IDq!="-" and IDs!="-" :

			C_s = set(UniProt_map[IDs][11])
			F_s = set(UniProt_map[IDs][12])
			P_s = set(UniProt_map[IDs][13])	

			if C_q!="-" and F_q!="-" and P_q!="-" and C_s!="-" and F_s!="-" and P_s!="-":

				if Nsimilar_foundC>=MaxN and Nsimilar_foundF>=MaxN and Nsimilar_foundP>=MaxN:
					break

				if len(C_q.intersection(C_s))>=(len(C_q)*0.8) and len(C_q.intersection(C_s))>=(len(C_s)*0.8) and Nsimilar_foundC <=MaxN:
					UniProt_map[IDq][14].append(IDs)
					Nsimilar_foundC += 1


				if len(F_q.intersection(F_s))>=(len(F_q)*0.8) and len(F_q.intersection(F_s))>=(len(F_s)*0.8) and Nsimilar_foundF <=MaxN:
					UniProt_map[IDq][15].append(IDs)
					Nsimilar_foundF += 1
			

				if len(P_q.intersection(P_s))>=(len(P_q)*0.8) and len(P_q.intersection(P_s))>=(len(P_s)*0.8) and Nsimilar_foundP <=MaxN:
					UniProt_map[IDq][16].append(IDs)
					Nsimilar_foundP += 1




#print(UniProt_map)
print("GO-similar proteins created")


# Parse the taxonomy names and record a dictionary with (Key: TaxID) a list that has: [Common Name, DivisionID]

Taxonomy_map = {}

with open(Tax_names_filename,"r") as fd:
	for line in fd:
		content = " ".join(line.split()).split("|")
		if content[3] == " scientific name ":
			TaxID = content[0].strip(" ")

			if TaxID in TaxIDs:
				Common_name = content[1].lstrip(" ").strip(" ")
				Taxonomy_map[TaxID] = [Common_name,1000]

Taxonomy_map_keys = Taxonomy_map.keys()

# add the DivisionID from Tax_nodes_filename
with open(Tax_nodes_filename,"r") as fd:
	for line in fd:
		content = " ".join(line.split()).split("|")
		TaxID = content[0].strip(" ")

		if TaxID in Taxonomy_map_keys:

			DivisionID = content[4].lstrip(" ").strip(" ")	
			Taxonomy_map[TaxID][1] = DivisionID

# Parse the Division_filename and add to Division_map that cointains Key: DivisionID and Value: Name

Division_map = {}

with open(Division_filename,"r") as fd:
	for line in fd:
		content = " ".join(line.split()).split("|")
		DivisionID = content[0].strip(" ")
		Name = content[2].lstrip(" ").strip(" ")
		Division_map[DivisionID] = Name

print("Taxonomy parsed")


# Parse the OrthoDB files and create a dictionary that coinatins Key: OG cluster ID and Value: [Cluster_name,[NCBIid(s) that belong to this cluster]]:

# Create a dictionary that maps NCBIids (values) to OrthoDB gene IDs (keys):

OrthoDB_genes = {}

with open(OrthoDB_genes_filename,"r") as fd:
	for line in fd:
		content = line.split("\t")
		NCBIid = content[5].strip()

		if NCBIid.endswith("\\N") is False and NCBIid in GeneInfo_map_keys:
			OrthoDB_genes[content[0].strip()] = NCBIid

print ("OrthoDB_genes created")

# Create a dictionary that maps OG Names (values) to OrthoDB OGs (keys):

OrthoDB_OGs = {}

with open(OrthoDB_OGnames_filename,"r") as fd:
	for line in fd:
		content = line.split("\t")
		Name = content[2].strip()

		if Name.endswith("\\N") is False:
			OrthoDB_OGs[content[0].strip()] = Name

print ("OrthoDB_OGs created")

# Create the final dictionary:

OrthoDB_map = {}

with open(OrthoDB_OG2genes_filename,"r") as fd:
	for line in fd:
		content = line.split("\t")
		OG = content[0].strip()
		Ortho_Gene = content[1].strip()
		
		if OG in OrthoDB_OGs.keys() and Ortho_Gene in OrthoDB_genes.keys():

			OG_name = OrthoDB_OGs[OG]
			NCBIid = OrthoDB_genes[Ortho_Gene]

			if OG in OrthoDB_map.keys():

				OrthoDB_map[OG][1].add(NCBIid)
			else:
				OrthoDB_map[OG] = [OG_name,set(NCBIid)]

print("OrthoDB parsed")



print("Printing")

############################################
############################################
############################################

# PRINT TABLES:

print(UniProt_map)

#### Gene ####

Gene_input = "id_NCBI\tgene_recommended_name\ttax_id\n"
Gene_input += "-\t-\t9606\n"

for NCBIid in GeneInfo_map:

	# The tax id has to be in Taxonomy_map
	if GeneInfo_map[NCBIid][1] in Taxonomy_map.keys():
		Gene_input += NCBIid+"\t"+GeneInfo_map[NCBIid][0]+"\t"+GeneInfo_map[NCBIid][1]+"\n"

fd = open("./Tables/Gene.tbl","w")
fd.write(Gene_input)
fd.close()

print("Gene printed")

#### Species ####

Species_input = "tax_id\tcommon_name\tdivision_id\n"

for TaxID in Taxonomy_map:

	# The division ID has to be in Division_map
	if Taxonomy_map[TaxID][1] in Division_map.keys():
		Species_input += TaxID+"\t"+Taxonomy_map[TaxID][0]+"\t"+Taxonomy_map[TaxID][1]+"\n"

fd = open("./Tables/Species.tbl","w")
fd.write(Species_input)
fd.close()

print("Species printed")

#### Taxonomy ####

Taxonomy_input = "division_id\tname_taxonomy\n"

for DivisionID in Division_map:
	Taxonomy_input += DivisionID+"\t"+Division_map[DivisionID]+"\n"

fd = open("./Tables/Taxonomy.tbl","w")
fd.write(Taxonomy_input)
fd.close()

print("Taxonomy printed")

#### GeneSynonyms ####

GeneSynonyms_input = "id_genesynonym\tid_NCBI\tname_genesynonym\n"

for NCBIid in GeneInfo_map:
	Synonyms = GeneInfo_map[NCBIid][2]

	#chech that there are synonyms
	#if len(Synonyms[0])>1:
	for Syn in Synonyms:
		GeneSynonyms_input += NCBIid+"_"+Syn+"\t"+NCBIid+"\t"+Syn+"\n"

fd = open("./Tables/GeneSynonyms.tbl","w")
fd.write(GeneSynonyms_input)
fd.close()

print("GeneSynonyms printed")

#### Proteins , ProteinSynonyms , Pfam ####

Proteins_input = "id_Uniprot\tid_NCBI\tprotein_recommended_name\tid_pfam\n"
ProteinSynonyms_input = "id_proteinsynonyms\tid_Uniprot\tname_proteinsynonym\n"
Pfam_input = "id_pfam\tname_pfam\n"

Printed_PFAMids = set()

for ID in UniProt_map:
	Content = UniProt_map[ID]

	#check that it has an NCBI_ID
	#if len(Content[0])>1:

	PfamID = "|".join(Content[5])
	PfamNames = "|".join(Content[6])

	#check that PfamID is not empty
	#if len(PfamID)>2:

	Proteins_input += ID+"\t"+Content[0]+"\t"+Content[2]+"\t"+PfamID+"\n"

	# Add to Pfam_input, just if it is not already there
	if PfamID not in Printed_PFAMids:
		Pfam_input += PfamID+"\t"+PfamNames+"\n"
		Printed_PFAMids.add(PfamID)

	# add ProteinSynonyms (AltName(s) and Short Name (s))

	Synonyms = Content[3]+Content[4]

	#if len(Synonyms)>0:
	#	if len(Synonyms[0])>2:
	for Syn in Synonyms:
		ProteinSynonyms_input += ID+"_"+Syn+"\t"+ID+"\t"+Syn+"\n"
	

fd = open("./Tables/Proteins.tbl","w")
fd.write(Proteins_input)
fd.close()

fd = open("./Tables/Pfam.tbl","w")
fd.write(Pfam_input)
fd.close()

fd = open("./Tables/ProteinSynonyms.tbl","w")
fd.write(ProteinSynonyms_input)
fd.close()

print("Proteins , ProteinSynonyms , Pfam printed")

#### OrthologueCluster and Gene_has_OrthologueCluster

OrthologueCluster_input = "ortho_cluster\tname_cluster\n"
Gene_has_OrthologueCluster_input = "Gene_id_NCBI\tOrthologueCluster_ortho_cluster\n"

for OG in OrthoDB_map:

	Name = OrthoDB_map[OG][0]
	NCBIids = OrthoDB_map[OG][1]

	for NCBIid in NCBIids:
		#check that we have the NCBIid in our other primary key
		if NCBIid in GeneInfo_map.keys():

			# Add to OrthologueCluster_input if it does not exist there
			if OrthologueCluster_input.find(OG)<0:
				OrthologueCluster_input += OG+"\t"+Name+"\n"

			# Add to Gene_has_OrthologueCluster_input
			Gene_has_OrthologueCluster_input += NCBIid+"\t"+OG+"\n"


fd = open("./Tables/OrthologueCluster.tbl","w")
fd.write(OrthologueCluster_input)
fd.close()

fd = open("./Tables/Gene_has_OrthologueCluster.tbl","w")
fd.write(Gene_has_OrthologueCluster_input)
fd.close()


print("OrthologueCluster and Gene_has_OrthologueCluster printed")

#### GeneOntology, Proteins_has_GeneOntology and SimilarGO

GeneOntology_input = "id_GO\ttype\tname\n"
Proteins_has_GeneOntology_input = "Proteins_id_Uniprot\tGeneOntology_id_GO\n"
Similar_GO_input = "id_similar_GO\tid_Uniprot_similar\tType_GO\tid_Uniprot\n"

id_similar_GO = 1 #this will be an arbitrary number

#GO (name)), [GOsC],[GOsF],[GOsP][SimilarGOsC], [SimilarGOsF], [SimilarGOsP]

for ID in UniProt_map.keys():

	GO_ids = UniProt_map[ID][8]
	GO_types = UniProt_map[ID][9]
	GO_names = UniProt_map[ID][10]

	# avoid entering proteins that are not in proteins
	if ID not in Proteins_input:
		continue

	for i in range(0,len(GO_ids)):

		# load GeneOntology
		id_GO = GO_ids[i]+GO_types[i]

		if id_GO not in GeneOntology_input:

			GeneOntology_input += id_GO+"\t"+GO_types[i]+"\t"+GO_names[i]+"\n"

		# load Proteins_has_GeneOntology
		Proteins_has_GeneOntology_input += ID+"\t"+id_GO+"\n"


	# load Similar_GO_input

	SimilarCs = UniProt_map[ID][14]
	SimilarFs = UniProt_map[ID][15]
	SimilarPs = UniProt_map[ID][16]

	for SimilarC in SimilarCs:
		if SimilarC != "-":
			Similar_GO_input += str(id_similar_GO)+"\t"+SimilarC+"\tC\t"+ID+"\n"
			id_similar_GO += 1

	for SimilarF in SimilarFs:
		if SimilarF != "-":
			Similar_GO_input += str(id_similar_GO)+"\t"+SimilarF+"\tF\t"+ID+"\n"
			id_similar_GO += 1

	for SimilarP in SimilarPs:
		if SimilarP != "-":
			Similar_GO_input += str(id_similar_GO)+"\t"+SimilarP+"\tP\t"+ID+"\n"
			id_similar_GO += 1


fd = open("./Tables/GeneOntology.tbl","w")
fd.write(GeneOntology_input)
fd.close()

fd = open("./Tables/Proteins_has_GeneOntology.tbl","w")
fd.write(Proteins_has_GeneOntology_input)
fd.close()

fd = open("./Tables/Similar_GO.tbl","w")
fd.write(Similar_GO_input)
fd.close()

print ("GO printed")
		











