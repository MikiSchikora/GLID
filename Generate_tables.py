# This script inputs all files got from the web and outputs all the tables for the MySQL database


# PROGRAM IMPLEMENTATION:

DataDir = "../Data/"

# Define imput files

import os


#### HUMAN ####

# #ID mapping file:
# os.system("awk -v FS='\\t' -v OFS='\\t' '{print $2,$3,$19}' "+DataDir+"HUMAN_9606_idmapping_selected.tab >" +DataDir+"HUMAN_9606_idmapping_selected_simple.tab")

# #Gene info file
# os.system("awk -v FS='\\t' -v OFS='\\t' '{print $1,$3,$5,$6}' "+DataDir+"Homo_sapiens.gene_info | grep Ensembl >" +DataDir+"Homo_sapiens_simple.gene_info")

# # define input files:

# IDmap_filename = DataDir+"HUMAN_9606_idmapping_selected_simple.tab"
# Sprot_filename = DataDir+"uniprot_sprot_human.dat"
# Gene_info_filename = DataDir+"Homo_sapiens_simple.gene_info"

#### ALL ####

#ID mapping file:
os.system("awk -v FS='\\t' -v OFS='\\t' '{print $2,$3,$19}' "+DataDir+"idmapping_selected.tab >" +DataDir+"idmapping_selected_simple.tab")

print("ID mapping parsed with awk")

#Gene info file
os.system("awk -v FS='\\t' -v OFS='\\t' '{print $1,$3,$5,$6}' "+DataDir+"gene_info | grep Ensembl >" +DataDir+"gene_info_simple")

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




##### PARSE STUFF #######

# Parse the Gene_info_filename and record into a dictionary (Key: ENSEMBLid) a list that has [RecName, TaxID, [Synonimous]]

GeneInfo_map = {}

with open(Gene_info_filename,"r") as fd:
	for line in fd:
		
		content = line.strip().split("\t")
		TaxID = content[0]
		RecName = content[1]
		Synonimous = content[2].split("|")

		IDs_array = content[3].split("|")

		I = 0
		for x in IDs_array:
			if x.startswith("Ensembl:"):
				ENSEMBLid = IDs_array[I].split(":")[1]
			I += 1
		
		GeneInfo_map[ENSEMBLid] = [RecName, TaxID, Synonimous]

print("Gene info parsed")


# Load ID mapping into dictionary of Tupples (key: UniProt_ID, Values: (NCBI_ID , ENSEBLid)):

IDmap = {}
with open(IDmap_filename,"r") as fd:
	for line in fd:
		content = line.strip("\n").split("\t")
		NCBI_ID = content[1]
		ENSEBLid = content[2].split(";")[0].strip()

		IDmap[content[0]] = (NCBI_ID,ENSEBLid)

print("ID mapping parsed")

# Parse the Sprot_filename and add to dictionary that cointains UniProt_ID as key and a list as values: (NCBI_geneID , Ensembl_geneID, RecName, AltName(s), Short Name (s), PFAM_domain(s), PFAM_domain(s) name(s),sequence, GO ID (s), GO type(s), GO (name))

UniProt_map = {}	
seq = ""
writing_seq = 0

with open(Sprot_filename,"r") as fd:

	prev_line = ""
	for line in fd:

		# start a new entry, adding gene_IDs:
		if line.startswith("ID"):
			ID = line.split()[1]
			UniProt_map[ID] = [IDmap[ID][0],IDmap[ID][1],'',[],[],[],[],'',[],[],[]]

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
				AltName = " ".join(AltName_info).split("=")[1].split(";")[0]
				UniProt_map[ID][3].append(AltName)

			# ShortName(s), in the form of a list that will be joined below:
			if line.split()[1].startswith("Short="):
				ShortName_info = line.split()[1:]
				if ShortName_info[-1].startswith('{') is True:
					ShortName_info.pop()
				ShortName = " ".join(ShortName_info).split("=")[1].split(";")[0]
				UniProt_map[ID][4].append(ShortName)

		# add Pfam domain IDs and names:
		if line.startswith("DR") and line.split()[1]=="Pfam;":
			Pfam_ID = line.split()[2].split(";")[0].strip()
			UniProt_map[ID][5].append(Pfam_ID)

			# add name
			Pfam_name = line.split()[3].split(";")[0].strip()
			UniProt_map[ID][6].append(Pfam_name)

		# add aa sequence:

		if prev_line.startswith("SQ") is True:
			writing_seq = 1

		if line.startswith("//") is True:
			UniProt_map[ID][7] = seq
			seq = ""
			writing_seq = 0

		if writing_seq == 1:
			seq += "".join(line.strip().split())


		# add GO information:

		if line.startswith("DR") and line.split()[1]=="GO;":

			GOid = line.split(":")[1].split(";")[0]
			Type_GO = line.split()[3].split(":")[0]
			Name_GO = line.split(":")[2].split(";")[0]

			UniProt_map[ID][8].append(GOid)
			UniProt_map[ID][9].append(Type_GO)
			UniProt_map[ID][10].append(Name_GO)

		# record the current line for the next run
		prev_line = line


print("Sprot parsed")

# Parse the OrthoDB files and create a dictionary that coinatins Key: OG cluster ID and Value: [Cluster_name,[ENSEMBLid(s) that belong to this cluster]]:

# # Create a dictionary that maps ENSEMBLids (values) to OrthoDB gene IDs (keys):

# OrthoDB_genes = {}

# with open(OrthoDB_genes_filename,"r") as fd:
# 	for line in fd:
# 		content = line.split("\t")
# 		if content[4].endswith("\\N") is False:
# 			OrthoDB_genes[content[0]] = content[4].strip()


# # Create a dictionary that maps OG Names (values) to OrthoDB OGs (keys):

# OrthoDB_OGs = {}

# with open(OrthoDB_OGnames_filename,"r") as fd:
# 	for line in fd:
# 		content = line.split("\t")
# 		if content[2].endswith("\\N") is False:
# 			OrthoDB_OGs[content[0]] = content[2].strip()

# #print(OrthoDB_OGs)

# # Create the final dictionary:

# OrthoDB_map = {}

# with open(OrthoDB_OG2genes_filename,"r") as fd:
# 	for line in fd:
# 		content = line.split("\t")
# 		OG = content[0]
# 		Ortho_Gene = content[1].strip()
		
# 		if OG in OrthoDB_OGs.keys() and Ortho_Gene in OrthoDB_genes.keys():
			
# 			OG_name = OrthoDB_OGs[OG]
# 			ENSEMBLid = OrthoDB_genes[Ortho_Gene]
# 			OrthoDB_map[OG] = [OG_name,[]]
# 			OrthoDB_map[OG][1].append(ENSEMBLid)

print("OrthoDB parsed")

# Parse the taxonomy names and record a dictionary with (Key: TaxID) a list that has: [Common Name, DivisionID]

Taxonomy_map = {}

with open(Tax_names_filename,"r") as fd:
	for line in fd:
		content = " ".join(line.split()).split("|")
		if content[3] == " scientific name ":
			TaxID = content[0].strip(" ")
			Common_name = content[1].lstrip(" ").strip(" ")
			Taxonomy_map[TaxID] = [Common_name,1000]

	# add the DivisionID from Tax_nodes_filename
with open(Tax_nodes_filename,"r") as fd:
	for line in fd:
		content = " ".join(line.split()).split("|")
		TaxID = content[0].strip(" ")
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





print("Printing")

############################################
############################################
############################################

# PRINT TABLES:

#### Gene ####

Gene_input = "id_ENSEMBL;gene_recommended_name;tax_id\n"

for ENSEMBLid in GeneInfo_map:

	# The tax id has to be in Taxonomy_map
	if GeneInfo_map[ENSEMBLid][1] in Taxonomy_map.keys():
		Gene_input += ENSEMBLid+";"+GeneInfo_map[ENSEMBLid][0]+";"+GeneInfo_map[ENSEMBLid][1]+"\n"

fd = open("./Tables/Gene.tbl","w")
fd.write(Gene_input)
fd.close()


#### Species ####

Species_input = "tax_id;common_name;division_id\n"

for TaxID in Taxonomy_map:

	# The division ID has to be in Division_map
	if Taxonomy_map[TaxID][1] in Division_map.keys():
		Species_input += TaxID+";"+Taxonomy_map[TaxID][0]+";"+Taxonomy_map[TaxID][1]+"\n"

fd = open("./Tables/Species.tbl","w")
fd.write(Species_input)
fd.close()


#### Taxonomy ####

Taxonomy_input = "division_id;name_taxonomy\n"

for DivisionID in Division_map:
	Taxonomy_input += DivisionID+";"+Division_map[DivisionID]+"\n"

fd = open("./Tables/Taxonomy.tbl","w")
fd.write(Taxonomy_input)
fd.close()


#### GeneSynonyms ####

GeneSynonyms_input = "id_genesynonym;id_ENSEMBL;name_genesynonym\n"

for ENSEMBLid in GeneInfo_map:
	Synonyms = GeneInfo_map[ENSEMBLid][2]

	#chech that there are synonyms
	if len(Synonyms[0])>1:
		for Syn in Synonyms:
			GeneSynonyms_input += ENSEMBLid+"_"+Syn+";"+ENSEMBLid+";"+Syn+"\n"

fd = open("./Tables/GeneSynonyms.tbl","w")
fd.write(GeneSynonyms_input)
fd.close()

#### Proteins , ProteinSynonyms , Pfam ####

Proteins_input = "id_Uniprot;id_ENSEMBL;protein_recommended_name;id_pfam\n"
ProteinSynonyms_input = "id_proteinsynonyms;id_Uniprot;name_proteinsynonym\n"
Pfam_input = "id_pfam;name_pfam\n"

for ID in UniProt_map:
	Content = UniProt_map[ID]

	#check that it has an Ensembl_ID
	if len(Content[1])>1:

		PfamID = "|".join(Content[5])
		PfamNames = "|".join(Content[6])

		#check that PfamID is not empty
		if len(PfamID)>1:

			Proteins_input += ID+";"+Content[1]+";"+Content[2]+";"+PfamID+"\n"

			# Add to Pfam_input, just if it is not already there
			if Pfam_input.find(PfamID)<0:

				Pfam_input += PfamID+";"+PfamNames+"\n"

			# add ProteinSynonyms (AltName(s) and Short Name (s))

			Synonyms = Content[3]+Content[4]

			if len(Synonyms)>0:
				if len(Synonyms[0])>1:
					for Syn in Synonyms:
						ProteinSynonyms_input += ID+"_"+Syn+";"+ID+";"+Syn+"\n"
	

fd = open("./Tables/Proteins.tbl","w")
fd.write(Proteins_input)
fd.close()

fd = open("./Tables/Pfam.tbl","w")
fd.write(Pfam_input)
fd.close()

fd = open("./Tables/ProteinSynonyms.tbl","w")
fd.write(ProteinSynonyms_input)
fd.close()

#(NCBI_geneID , Ensembl_geneID, RecName, AltName(s), Short Name (s), PFAM_domain(s), PFAM_domain(s) name(s),sequence, GO ID (s), GO type(s), GO (name))





