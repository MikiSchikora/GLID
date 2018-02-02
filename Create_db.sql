-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema glid
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema glid
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `glid` DEFAULT CHARACTER SET utf8 ;
USE `glid` ;

-- -----------------------------------------------------
-- Table `Taxonomy`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Taxonomy` ;

CREATE TABLE IF NOT EXISTS `Taxonomy` (
  `division_id` INT NOT NULL,
  `name_taxonomy` VARCHAR(100) NULL,
  PRIMARY KEY (`division_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Species`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Species` ;

CREATE TABLE IF NOT EXISTS `Species` (
  `tax_id` VARCHAR(30) NOT NULL,
  `common_name` VARCHAR(100) NULL,
  `division_id` INT NOT NULL,
  PRIMARY KEY (`tax_id`),
  INDEX `fk_species_specifictaxonomy_idx` (`division_id` ASC),
  CONSTRAINT `fk_species_specifictaxonomy`
    FOREIGN KEY (`division_id`)
    REFERENCES `Taxonomy` (`division_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Gene`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Gene` ;

CREATE TABLE IF NOT EXISTS `Gene` (
  `id_ENTREZGENE` VARCHAR(30) NOT NULL,
  `gene_recommended_name` VARCHAR(100) NULL,
  `tax_id` VARCHAR(30) NULL,
  PRIMARY KEY (`id_ENTREZGENE`),
  INDEX `fk_gene_species_idx` (`tax_id` ASC),
  CONSTRAINT `fk_gene_species`
    FOREIGN KEY (`tax_id`)
    REFERENCES `Species` (`tax_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Pfam`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Pfam` ;

CREATE TABLE IF NOT EXISTS `Pfam` (
  `id_pfam` VARCHAR(200) NOT NULL,
  `name_pfam` VARCHAR(100) NULL,
  PRIMARY KEY (`id_pfam`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Proteins`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Proteins` ;

CREATE TABLE IF NOT EXISTS `Proteins` (
  `id_Uniprot` VARCHAR(30) NOT NULL,
  `id_ENTREZGENE` VARCHAR(20) NOT NULL,
  `prot_recommended_name` VARCHAR(200) NULL,
  `id_pfam` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id_Uniprot`),
  INDEX `fk_protein_pfam_idx` (`id_pfam` ASC),
  INDEX `fk_protein_gene_idx` (`id_ENTREZGENE` ASC),
  CONSTRAINT `fk_protein_gene`
    FOREIGN KEY (`id_ENTREZGENE`)
    REFERENCES `Gene` (`id_ENTREZGENE`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_protein_pfam`
    FOREIGN KEY (`id_pfam`)
    REFERENCES `Pfam` (`id_pfam`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `OrthologueCluster`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `OrthologueCluster` ;

CREATE TABLE IF NOT EXISTS `OrthologueCluster` (
  `ortho_cluster` VARCHAR(30) NOT NULL,
  `name_cluster` VARCHAR(200) NULL,
  PRIMARY KEY (`ortho_cluster`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Gene_has_OrthologueCluster`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Gene_has_OrthologueCluster` ;

CREATE TABLE IF NOT EXISTS `Gene_has_OrthologueCluster` (
  `Gene_id_ENTREZGENE` VARCHAR(30) NOT NULL,
  `OrthologueCluster_ortho_cluster` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`Gene_id_ENTREZGENE`, `OrthologueCluster_ortho_cluster`),
  INDEX `fk_Gene_has_OrthologueCluster_OrthologueCluster1_idx` (`OrthologueCluster_ortho_cluster` ASC),
  INDEX `fk_Gene_has_OrthologueCluster_Gene1_idx` (`Gene_id_ENTREZGENE` ASC),
  CONSTRAINT `fk_Gene_has_OrthologueCluster_Gene1`
    FOREIGN KEY (`Gene_id_ENTREZGENE`)
    REFERENCES `Gene` (`id_ENTREZGENE`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Gene_has_OrthologueCluster_OrthologueCluster1`
    FOREIGN KEY (`OrthologueCluster_ortho_cluster`)
    REFERENCES `OrthologueCluster` (`ortho_cluster`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `GeneOntology`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GeneOntology` ;

CREATE TABLE IF NOT EXISTS `GeneOntology` (
  `id_GO` VARCHAR(15) NOT NULL,
  `type` VARCHAR(45) NULL COMMENT 'Biological Process\nMolecular Function\nCellular Component',
  `name` VARCHAR(200) NULL COMMENT 'Description',
  PRIMARY KEY (`id_GO`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `Proteins_has_GeneOntology`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `Proteins_has_GeneOntology` ;

CREATE TABLE IF NOT EXISTS `Proteins_has_GeneOntology` (
  `Proteins_id_Uniprot` VARCHAR(30) NOT NULL,
  `GeneOntology_id_GO` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`Proteins_id_Uniprot`, `GeneOntology_id_GO`),
  INDEX `fk_Proteins_has_GeneOntology_GeneOntology1_idx` (`GeneOntology_id_GO` ASC),
  INDEX `fk_Proteins_has_GeneOntology_Proteins1_idx` (`Proteins_id_Uniprot` ASC),
  CONSTRAINT `fk_Proteins_has_GeneOntology_Proteins1`
    FOREIGN KEY (`Proteins_id_Uniprot`)
    REFERENCES `Proteins` (`id_Uniprot`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Proteins_has_GeneOntology_GeneOntology1`
    FOREIGN KEY (`GeneOntology_id_GO`)
    REFERENCES `GeneOntology` (`id_GO`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `GeneSynonyms`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `GeneSynonyms` ;

CREATE TABLE IF NOT EXISTS `GeneSynonyms` (
  `id_genesynonym` VARCHAR(50) NOT NULL COMMENT 'id_genesynonym = genesynonyms_taxid',
  `id_ENTREZGENE` VARCHAR(30) NULL,
  `name_genesynonym` VARCHAR(100) NULL,
  PRIMARY KEY (`id_genesynonym`),
  INDEX `gene_genesynonyms_idx` (`id_ENTREZGENE` ASC),
  CONSTRAINT `fk_gene_genesynonyms`
    FOREIGN KEY (`id_ENTREZGENE`)
    REFERENCES `Gene` (`id_ENTREZGENE`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `ProteinSynonyms`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `ProteinSynonyms` ;

CREATE TABLE IF NOT EXISTS `ProteinSynonyms` (
  `id_proteinsynonyms` VARCHAR(300) NOT NULL COMMENT 'id_proteinsynonym = proteinsynonyms_taxid',
  `id_Uniprot` VARCHAR(30) NULL,
  `name_proteinsynonym` VARCHAR(100) NULL,
  PRIMARY KEY (`id_proteinsynonyms`),
  INDEX `fk_protein_proteinsynonyms_idx` (`id_Uniprot` ASC),
  CONSTRAINT `fk_protein_proteinsynonyms`
    FOREIGN KEY (`id_Uniprot`)
    REFERENCES `Proteins` (`id_Uniprot`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
