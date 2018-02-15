-- Previous step: Forward Engineer = data model design -> database (empty)

USE glid;

-- TO DELETE TABLES:
SET SQL_SAFE_UPDATES = 0;
SET FOREIGN_KEY_CHECKS=0;

-- AFTER:
SET SQL_SAFE_UPDATES = 1;
SET FOREIGN_KEY_CHECKS=1;
-- 
-- Load data into tables

-- Taxonomy 
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Taxonomy.tbl'
INTO TABLE Taxonomy
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Species
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Species.tbl'
INTO TABLE Species
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Pfam 
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Pfam.tbl'
INTO TABLE Pfam
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Gene 
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Gene.tbl'
INTO TABLE Gene
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Proteins
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Proteins.tbl'
INTO TABLE Proteins
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- OrthologueCluster
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/OrthologueCluster.tbl'
INTO TABLE OrthologueCluster
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- GeneSynonyms (no header)

LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/GeneSynonyms.tbl'
INTO TABLE GeneSynonyms
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- ProteinSynonyms 

LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/ProteinSynonyms.tbl'
INTO TABLE ProteinSynonyms
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Gene_has_OrthologueCluster
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Gene_has_OrthologueCluster.tbl'
INTO TABLE Gene_has_OrthologueCluster
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- GeneOntology
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/GeneOntology.tbl'
INTO TABLE GeneOntology
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Proteins_has_GeneOntology
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Proteins_has_GeneOntology.tbl'
INTO TABLE Proteins_has_GeneOntology
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Similar_GO
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Similar_GO.tbl'
INTO TABLE Similar_GO
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;







