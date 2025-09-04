<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250903140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create v2_invitations table for company user invitations';
    }

    public function up(Schema $schema): void
    {
        // Create v2_invitations table
        $this->addSql('CREATE TABLE v2_invitations (
            id INT AUTO_INCREMENT NOT NULL,
            customer_id INT NOT NULL,
            invited_by_id INT NOT NULL,
            accepted_by_id INT DEFAULT NULL,
            email VARCHAR(255) NOT NULL,
            first_name VARCHAR(100) NOT NULL,
            last_name VARCHAR(100) NOT NULL,
            customer_role VARCHAR(50) NOT NULL DEFAULT "employee",
            token VARCHAR(255) NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT "pending",
            created_at DATETIME NOT NULL,
            expires_at DATETIME NOT NULL,
            accepted_at DATETIME DEFAULT NULL,
            UNIQUE INDEX UNIQ_INVITATION_TOKEN (token),
            INDEX IDX_INVITATION_CUSTOMER (customer_id),
            INDEX IDX_INVITATION_INVITED_BY (invited_by_id),
            INDEX IDX_INVITATION_ACCEPTED_BY (accepted_by_id),
            INDEX IDX_INVITATION_EMAIL (email),
            INDEX IDX_INVITATION_STATUS (status),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');

        // Add foreign key constraints
        $this->addSql('ALTER TABLE v2_invitations ADD CONSTRAINT FK_V2_INVITATION_CUSTOMER 
            FOREIGN KEY (customer_id) REFERENCES v2_customers (id) ON DELETE CASCADE');
        
        $this->addSql('ALTER TABLE v2_invitations ADD CONSTRAINT FK_V2_INVITATION_INVITED_BY 
            FOREIGN KEY (invited_by_id) REFERENCES v2_customer_users (id) ON DELETE CASCADE');
        
        $this->addSql('ALTER TABLE v2_invitations ADD CONSTRAINT FK_V2_INVITATION_ACCEPTED_BY 
            FOREIGN KEY (accepted_by_id) REFERENCES v2_customer_users (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE v2_invitations DROP FOREIGN KEY FK_V2_INVITATION_CUSTOMER');
        $this->addSql('ALTER TABLE v2_invitations DROP FOREIGN KEY FK_V2_INVITATION_INVITED_BY');
        $this->addSql('ALTER TABLE v2_invitations DROP FOREIGN KEY FK_V2_INVITATION_ACCEPTED_BY');
        $this->addSql('DROP TABLE v2_invitations');
    }
}