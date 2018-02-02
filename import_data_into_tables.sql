-- Previous step: Forward Engineer = data model design -> database (empty)

USE glid;

-- SHOW TABLES;

SET SQL_SAFE_UPDATES = 0;

-- Load data into tables

-- Taxonomy 
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID_copy/Tables/Taxonomy.tbl'
INTO TABLE Taxonomy
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;


-- Species
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID_copy/Tables/Species.tbl'
INTO TABLE Species
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;


-- Pfam 
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID_copy/Tables/Pfam.tbl'
INTO TABLE Pfam
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

DESCRIBE Pfam;


SELECT * FROM Pfam limit 10;

-- Gene 
-- 
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID_copy/Tables/Gene.tbl'
INTO TABLE Gene
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Proteins
SET FOREIGN_KEY_CHECKS=0; -- to avoid problems with id_Pfam FK (it is trying to find all rows of Proteins in all rows in Pfam)

LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID_copy/Tables/Proteins.tbl'
INTO TABLE Proteins
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

SET FOREIGN_KEY_CHECKS=1

-- GeneSynonyms (no header)
-- SET FOREIGN_KEY_CHECKS=0; 

LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID_copy/Tables/nh_GeneSynonyms.tbl'
INTO TABLE GeneSynonyms
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
;

-- SET FOREIGN_KEY_CHECKS=1;
-- 
-- ProteinSynonyms (no header)
-- SET FOREIGN_KEY_CHECKS=0; 

LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID_copy/Tables/nh_ProteinSynonyms.tbl'
INTO TABLE ProteinSynonyms
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
;

-- SET FOREIGN_KEY_CHECKS=1;

-- Gene_has_OrthologueCluster (no header)
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID_copy/Tables/nh_Gene_has_OrthologueCluster.tbl'
INTO TABLE Gene_has_OrthologueCluster
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
;

-- OrthologueCluster
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID_copy/Tables/OrthologueCluster.tbl'
INTO TABLE OrthologueCluster
FIELDS TERMINATED BY '\t'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;









