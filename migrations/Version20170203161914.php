<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Adds application_conf and school_conf tables.
 */
final class Version20170203161914 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE application_config (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(200) NOT NULL, value LONGTEXT NOT NULL, UNIQUE INDEX app_conf_uniq (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE school_config (id INT AUTO_INCREMENT NOT NULL, school_id INT DEFAULT NULL, name VARCHAR(200) NOT NULL, value LONGTEXT NOT NULL, INDEX IDX_29362CB0C32A47EE (school_id), UNIQUE INDEX school_conf_uniq (school_id, name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE school_config ADD CONSTRAINT FK_29362CB0C32A47EE FOREIGN KEY (school_id) REFERENCES school (school_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE application_config');
        $this->addSql('DROP TABLE school_config');
    }
}
