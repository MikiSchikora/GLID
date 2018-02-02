-- Previous step: Forward Engineer = data model design -> database (empty)

USE glid;

-- SHOW TABLES;

SET SQL_SAFE_UPDATES = 0;

-- Load data into tables

-- Taxonomy 
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Taxonomy.csv'
INTO TABLE Taxonomy
FIELDS TERMINATED BY ';'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

DESCRIBE Taxonomy;

SELECT * FROM Taxonomy;

-- Species
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Species.csv'
INTO TABLE Species
FIELDS TERMINATED BY ';'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

-- Pfam 
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Pfam.csv'
INTO TABLE Pfam
FIELDS TERMINATED BY ';'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

DESCRIBE Pfam;


SELECT * FROM Pfam limit 10;

-- Gene 
-- 
-- LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Gene.csv'
-- INTO TABLE Gene
-- FIELDS TERMINATED BY ';'
-- LINES TERMINATED BY '\n'
-- IGNORE 1 ROWS;
-- 
-- -- mysql> LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Gene.csv'
-- --     -> INTO TABLE Gene
-- --     -> FIELDS TERMINATED BY ';'
-- --     -> LINES TERMINATED BY '\n'
-- --     -> IGNORE 1 ROWS;
-- -- Query OK, 0 rows affected, 65535 warnings (35.79 sec)
-- -- Records: 497961  Deleted: 0  Skipped: 497961  Warnings: 498321
-- 
-- -- SHOW WARNINGS;
-- -- -- | Warning | 1452 | Cannot add or update a child row: a foreign key constraint fails (`glid`.`gene`, CONSTRAINT `fk_gene_species` FOREIGN KEY (`tax_id`) REFERENCES `Species` (`tax_id`) ON DELETE NO ACTION ON UPDATE NO ACTION) |
-- -- 
-- -- Proteins
-- 
-- LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Proteins.csv'
-- INTO TABLE Proteins
-- FIELDS TERMINATED BY ';'
-- LINES TERMINATED BY '\n'
-- IGNORE 1 ROWS;