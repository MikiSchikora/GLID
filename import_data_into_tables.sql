-- Previous step: Forward Engineer = data model design -> database (empty)

USE glid;

SHOW TABLES;

-- Load data into tables

-- Taxonomy 
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Taxonomy.csv'
INTO TABLE Taxonomy
FIELDS TERMINATED BY ';'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

DESCRIBE Taxonomy;

SELECT * FROM Taxonomy;

-- Pfam 
LOAD DATA LOCAL INFILE '/Users/Aida/Documents/Bioinformatics_Master/2nd_TERM/DBW/project/GLID/Tables/Pfam.csv'
INTO TABLE Pfam
FIELDS TERMINATED BY ';'
LINES TERMINATED BY '\n'
IGNORE 1 ROWS;

DESCRIBE Pfam;

SELECT * FROM Pfam;


