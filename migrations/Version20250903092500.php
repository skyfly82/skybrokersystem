<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250903092500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create v2_customers, v2_customer_users, and v2_system_users tables';
    }

    public function up(Schema $schema): void
    {
        // Create v2_customers table
        $this->addSql('CREATE TABLE v2_customers (
            id INT AUTO_INCREMENT NOT NULL,
            company_name VARCHAR(255) NOT NULL,
            vat_number VARCHAR(20) DEFAULT NULL,
            regon VARCHAR(20) DEFAULT NULL,
            address LONGTEXT DEFAULT NULL,
            postal_code VARCHAR(10) DEFAULT NULL,
            city VARCHAR(100) DEFAULT NULL,
            country VARCHAR(100) DEFAULT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            email VARCHAR(255) DEFAULT NULL,
            type VARCHAR(50) NOT NULL DEFAULT "business",
            status VARCHAR(50) NOT NULL DEFAULT "active",
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create v2_customer_users table
        $this->addSql('CREATE TABLE v2_customer_users (
            id INT AUTO_INCREMENT NOT NULL,
            customer_id INT NOT NULL,
            email VARCHAR(180) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            customer_role VARCHAR(50) NOT NULL DEFAULT "employee",
            status VARCHAR(50) NOT NULL DEFAULT "active",
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            last_login_at DATETIME DEFAULT NULL,
            email_verified_at DATETIME DEFAULT NULL,
            UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email),
            INDEX IDX_CUSTOMER_USER_CUSTOMER (customer_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Create v2_system_users table
        $this->addSql('CREATE TABLE v2_system_users (
            id INT AUTO_INCREMENT NOT NULL,
            email VARCHAR(180) NOT NULL,
            roles JSON NOT NULL,
            password VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            phone VARCHAR(20) DEFAULT NULL,
            department VARCHAR(50) NOT NULL DEFAULT "support",
            position VARCHAR(100) DEFAULT NULL,
            status VARCHAR(50) NOT NULL DEFAULT "active",
            created_at DATETIME NOT NULL,
            updated_at DATETIME DEFAULT NULL,
            last_login_at DATETIME DEFAULT NULL,
            email_verified_at DATETIME DEFAULT NULL,
            UNIQUE INDEX UNIQ_SYSTEM_USER_EMAIL (email),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign key constraint
        $this->addSql('ALTER TABLE v2_customer_users ADD CONSTRAINT FK_V2_CUSTOMER_USER_CUSTOMER 
            FOREIGN KEY (customer_id) REFERENCES v2_customers (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE v2_customer_users DROP FOREIGN KEY FK_V2_CUSTOMER_USER_CUSTOMER');
        $this->addSql('DROP TABLE v2_customer_users');
        $this->addSql('DROP TABLE v2_system_users');
        $this->addSql('DROP TABLE v2_customers');
    }
}