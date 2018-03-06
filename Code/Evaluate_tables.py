# This script is for evaluating the tables

TablesDir = "./Tables/"

with open(TablesDir+"Proteins.tbl") as fd:

	Proteins_geneIDs = set([line.split("\t")[1] for line in fd])

with open(TablesDir+"GeneSynonyms.tbl") as fd:

	GeneSynonyms_geneIDs = set([line.split("\t")[1] for line in fd])

with open(TablesDir+"ProteinSynonyms.tbl") as fd:

	ProtSynonyms_IDs = set([line.split("\t")[1] for line in fd])

with open(TablesDir+"Species.tbl") as fd:

	TaxIDs = set([line.split("\t")[0] for line in fd])	

with open(TablesDir+"Gene_has_OrthologueCluster.tbl") as fd:

	GenesWithOrthologueCluster = set([line.split("\t")[0] for line in fd])		

with open(TablesDir+"Gene.tbl") as fd:

	GeneIDs_inGene = set([line.split("\t")[0] for line in fd])	

with open(TablesDir+"Pfam.tbl") as fd:

	PFAM_ids = set([line.split("\t")[0] for line in fd])	

with open(TablesDir+"Proteins_has_GeneOntology.tbl") as fd:

	ProteinsWithGO = set([line.split("\t")[0] for line in fd])		

with open(TablesDir+"Similar_GO.tbl") as fd:

	ProteinsWithSimilarGO = set([line.split("\t")[3] for line in fd])	

with open(TablesDir+"Gene.tbl") as fd:

	for line in fd:

		geneID = line.split("\t")[0]
		RecName = line.split("\t")[1]
		taxID = line.split("\t")[2]
		#print(taxID)

		# Does each gene have a protein?	

		if geneID not in Proteins_geneIDs:

			pass
			#print(line) ##### only the gene - has no protein ##### GOOD

		# Does each gene have a gene synonym?

		if geneID not in GeneSynonyms_geneIDs:

			pass
			#print(line) ##### only the gene - has no gene synonym  ##### GOOD

		# Does each gene have a tax id?

		if taxID not in TaxIDs:

			pass # everything is good ##### GOOD


		# Does each gene have an entry to Gene_has_OrthologueCluster 

		## (only 5536 genes have such a thing)?

		#print(len(GenesWithOrthologueCluster))
		
		if geneID in GenesWithOrthologueCluster:

			
			pass
			#print(line)

		# some example genes:

		# 2538689 sum3    4896

		# 2538693 SPCC777.03c     4896

		# 2538695 taf13   4896

		# 2538715 rpl3702 4896

		# 2538722 imt2    4896

		# 2538727 SPCC757.06      4896

		# 2538732 ght1    4896

		# 2538733 SPCC663.08c     4896

		# 2538741 ynd1    4896

		# 2538749 SPCC1672.11c    4896

		# 2538755 Tf2-12  4896

		# 2538758 ppe1    4896

		# 2538772 oca8    4896

		# 2538779 SPCC569.07      4896

		# 2538784 tim23   4896

		# 2538786 SPCC830.06      4896




with open(TablesDir+"Proteins.tbl","r") as fd:

	for line in fd:

		ID = line.split("\t")[0]
		geneID = line.split("\t")[1]
		pfamID = line.split("\t")[3].strip()

		#print(pfamID)

		# Does each protein have a valid gene name ?

		if geneID not in GeneIDs_inGene:

			#print(line)
			pass
			# all proteins have a gene ##### GOOD

		# Does each protein have a PFAM_ID?

		if pfamID not in PFAM_ids:

			pass
			#print(line)
			

			# All correct # GOOD!!!!!

		# Does each protein have a protein synonym?

		if ID not in ProtSynonyms_IDs:

			pass

			#print(line)

			# Everything is correct #GOOD


		# Does each protein have a similar GO (subdivide in C,F,P)?

			# this needs the whole table.

		# Does each protein have an entry to Proteins has Proteins_has_GO

		if ID not in ProteinsWithGO:

			#print(line)
			pass

			# Everything is correct #GOOD





