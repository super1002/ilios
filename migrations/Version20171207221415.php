<?php
declare(strict_types=1);

namespace Ilios\Migrations;

use App\Classes\MysqlMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Associates users as administrators with curriculum inventory reports.
 */
final class Version20171207221415 extends MysqlMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE TABLE curriculum_inventory_report_administrator (report_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_730428DB4BD2A4C0 (report_id), INDEX IDX_730428DBA76ED395 (user_id), PRIMARY KEY(report_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE curriculum_inventory_report_administrator ADD CONSTRAINT FK_730428DB4BD2A4C0 FOREIGN KEY (report_id) REFERENCES curriculum_inventory_report (report_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE curriculum_inventory_report_administrator ADD CONSTRAINT FK_730428DBA76ED395 FOREIGN KEY (user_id) REFERENCES user (user_id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE curriculum_inventory_report_administrator');
    }
}
