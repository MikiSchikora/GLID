# This script inputs all files got from the web and outputs all the tables for the MySQL database


# PROGRAM IMPLEMENTATION:

DataDir = "../Data/"
#print("adeu")

# Process the files with bash

import os

#ID mapping file:
os.system("awk -v FS='\\t' -v OFS='\\t' '{print $2,$3,$19}' "+DataDir+"HUMAN_9606_idmapping_selected.tab >" +DataDir+"HUMAN_9606_idmapping_selected_simple.tab")

#Gene info file
os.system("awk -v FS='\\t' -v OFS='\\t' '{print $1,$3,$5,$6}' "+DataDir+"Homo_sapiens.gene_info | grep Ensembl >" +DataDir+"Homo_sapiens_simple.gene_info")


# define input files:

IDmap_filename = DataDir+"HUMAN_9606_idmapping_selected_simple.tab"
Sprot_filename = DataDir+"uniprot_sprot_human.dat"
Gene_info_filename = DataDir+"Homo_sapiens_simple.gene_info"

# define output files:

#UniProt_integrated_filename = DataDir+"Uniprot_integrated_human.tab"
#Multifasta_filename = DataDir+"Sprot_human.fa"


# Parse the Gene_info_filename and record into a dictionary (Key: ENSEMBLid) a list that has [RecName, TaxID, [Synonimous]]

GeneInfo_map = {}

with open(Gene_info_filename,"r") as fd:
	for line in fd:
		content = line.strip().split("\t")
		
		print(content)
		#GeneInfo_map[ENSEMBLid] = [RecName, TaxID, [Synonimous]]





# Load ID mapping into dictionary of Tupples (key: UniProt_ID, Values: (NCBI_ID , ENSEBLid)):

IDmap = {}
with open(IDmap_filename,"r") as fd:
	for line in fd:
		content = line.strip("\n").split("\t")
		NCBI_ID = content[1]
		ENSEBLid = content[2]
		if ENSEBLid.find(";") is True:
			ENSEBLids = ENSEBLid.split(";")
			ENSEBLid = ENSEBLids[0]

		IDmap[content[0]] = (NCBI_ID,ENSEBLid)

# Parse the Sprot_filename and add to dictionary that cointains UniProt_ID as key and a list as values: (NCBI_geneID , Ensembl_geneID, RecName, AltName(s), Short Name (s), PFAM_domain(s), sequence, GO ID (s), GO type(s), GO (name))

UniProt_map = {}	
seq = ""
writing_seq = 0

with open(Sprot_filename,"r") as fd:

	prev_line = ""
	for line in fd:

		# start a new entry, adding gene_IDs:
		if line.startswith("ID"):
			ID = line.split()[1]
			UniProt_map[ID] = [IDmap[ID][0],IDmap[ID][1],'',[],[],[],'',[],[],[]]

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

		# add Pfam domain IDs:
		if line.startswith("DR") and line.split()[1]=="Pfam;":
			Pfam_ID = line.split()[2].split(";")[0]
			UniProt_map[ID][5].append(Pfam_ID)

		# add aa sequence:

		if prev_line.startswith("SQ") is True:
			writing_seq = 1

		if line.startswith("//") is True:
			UniProt_map[ID][6] = seq
			seq = ""
			writing_seq = 0

		if writing_seq == 1:
			seq += "".join(line.strip().split())


		# add GO information:

		if line.startswith("DR") and line.split()[1]=="GO;":

			GOid = line.split(":")[1].split(";")[0]
			Type_GO = line.split()[3].split(":")[0]
			Name_GO = line.split(":")[2].split(";")[0]

			UniProt_map[ID][7].append(GOid)
			UniProt_map[ID][8].append(Type_GO)
			UniProt_map[ID][9].append(Name_GO)

		# record the current line for the next run
		prev_line = line

print(UniProt_map)










