<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250903Step1PreliminaryRegistration extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create v2_preliminary_registrations table for step 1 registration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE v2_preliminary_registrations (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password_hash VARCHAR(255) NOT NULL, customer_type VARCHAR(20) NOT NULL, nip VARCHAR(32) DEFAULT NULL, country CHAR(2) DEFAULT NULL, unregistered_business TINYINT(1) NOT NULL, b2b TINYINT(1) NOT NULL, status VARCHAR(50) NOT NULL, sso_provider VARCHAR(50) DEFAULT NULL, token VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_V2_PRELIMINARY_REGISTRATIONS_TOKEN (token), PRIMARY KEY(id)) DEFAULT CHARACTER SET UTF8MB4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE v2_preliminary_registrations');
    }
}

