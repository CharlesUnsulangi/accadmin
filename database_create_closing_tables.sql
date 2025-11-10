-- =============================================
-- Multi-Layer Closing System - Table Creation
-- Database: RCM_DEV_HGS_SB
-- Created: 2025-11-10
-- =============================================

USE RCM_DEV_HGS_SB;
GO

-- =============================================
-- Table 1: tr_acc_monthly_closing
-- Monthly Closing Records (Layer 1)
-- =============================================
IF OBJECT_ID('dbo.tr_acc_monthly_closing', 'U') IS NOT NULL
    DROP TABLE dbo.tr_acc_monthly_closing;
GO

CREATE TABLE dbo.tr_acc_monthly_closing (
    -- Primary Key
    monthly_closing_id INT IDENTITY(1,1) NOT NULL,
    
    -- Versioning Fields
    version_number INT NOT NULL DEFAULT 1,
    version_status VARCHAR(20) NOT NULL DEFAULT 'DRAFT', -- DRAFT, ACTIVE, SUPERSEDED, ARCHIVED
    
    -- Period Information
    closing_year INT NOT NULL,
    closing_month INT NOT NULL, -- 1-12
    closing_date DATE NOT NULL,
    
    -- COA Hierarchy (for reporting flexibility)
    coa_main_id INT NULL,
    coasub1_id INT NULL,
    coasub2_id INT NULL,
    coa_id INT NULL,
    
    -- Balance Fields
    saldo_awal DECIMAL(18,2) NOT NULL DEFAULT 0,
    saldo_akhir DECIMAL(18,2) NOT NULL DEFAULT 0,
    mutasi_debet DECIMAL(18,2) NOT NULL DEFAULT 0,
    mutasi_kredit DECIMAL(18,2) NOT NULL DEFAULT 0,
    
    -- Audit Fields
    created_by VARCHAR(100) NULL,
    created_date DATETIME NOT NULL DEFAULT GETDATE(),
    modified_by VARCHAR(100) NULL,
    modified_date DATETIME NULL,
    approved_by VARCHAR(100) NULL,
    approved_date DATETIME NULL,
    
    -- Record Status
    rec_status CHAR(1) NOT NULL DEFAULT '1', -- 1=Active, 0=Inactive
    
    -- Constraints
    CONSTRAINT PK_monthly_closing PRIMARY KEY (monthly_closing_id),
    CONSTRAINT CHK_monthly_closing_month CHECK (closing_month BETWEEN 1 AND 12),
    CONSTRAINT CHK_monthly_closing_status CHECK (version_status IN ('DRAFT', 'ACTIVE', 'SUPERSEDED', 'ARCHIVED')),
    CONSTRAINT CHK_monthly_closing_rec_status CHECK (rec_status IN ('0', '1'))
);
GO

-- Index for performance
CREATE INDEX IX_monthly_closing_period ON dbo.tr_acc_monthly_closing(closing_year, closing_month);
CREATE INDEX IX_monthly_closing_version ON dbo.tr_acc_monthly_closing(version_status);
CREATE INDEX IX_monthly_closing_coa ON dbo.tr_acc_monthly_closing(coa_main_id, coasub1_id, coasub2_id, coa_id);
GO

-- =============================================
-- Table 2: tr_acc_yearly_closing
-- Yearly Closing Records (Layer 2)
-- =============================================
IF OBJECT_ID('dbo.tr_acc_yearly_closing', 'U') IS NOT NULL
    DROP TABLE dbo.tr_acc_yearly_closing;
GO

CREATE TABLE dbo.tr_acc_yearly_closing (
    -- Primary Key
    yearly_closing_id INT IDENTITY(1,1) NOT NULL,
    
    -- Versioning Fields
    version_number INT NOT NULL DEFAULT 1,
    version_status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
    
    -- Period Information
    closing_year INT NOT NULL,
    closing_date DATE NOT NULL,
    
    -- COA Hierarchy
    coa_main_id INT NULL,
    coasub1_id INT NULL,
    coasub2_id INT NULL,
    coa_id INT NULL,
    
    -- Balance Fields (Aggregated from Monthly)
    saldo_awal DECIMAL(18,2) NOT NULL DEFAULT 0,
    saldo_akhir DECIMAL(18,2) NOT NULL DEFAULT 0,
    total_debet DECIMAL(18,2) NOT NULL DEFAULT 0,
    total_kredit DECIMAL(18,2) NOT NULL DEFAULT 0,
    
    -- Month-by-Month Summary (Optional JSON field for detailed tracking)
    monthly_summary NVARCHAR(MAX) NULL, -- JSON format
    
    -- Audit Fields
    created_by VARCHAR(100) NULL,
    created_date DATETIME NOT NULL DEFAULT GETDATE(),
    modified_by VARCHAR(100) NULL,
    modified_date DATETIME NULL,
    approved_by VARCHAR(100) NULL,
    approved_date DATETIME NULL,
    
    -- Record Status
    rec_status CHAR(1) NOT NULL DEFAULT '1',
    
    -- Constraints
    CONSTRAINT PK_yearly_closing PRIMARY KEY (yearly_closing_id),
    CONSTRAINT CHK_yearly_closing_status CHECK (version_status IN ('DRAFT', 'ACTIVE', 'SUPERSEDED', 'ARCHIVED')),
    CONSTRAINT CHK_yearly_closing_rec_status CHECK (rec_status IN ('0', '1'))
);
GO

-- Index for performance
CREATE INDEX IX_yearly_closing_year ON dbo.tr_acc_yearly_closing(closing_year);
CREATE INDEX IX_yearly_closing_version ON dbo.tr_acc_yearly_closing(version_status);
CREATE INDEX IX_yearly_closing_coa ON dbo.tr_acc_yearly_closing(coa_main_id, coasub1_id, coasub2_id, coa_id);
GO

-- =============================================
-- Table 3: tr_acc_yearly_audit
-- Yearly Audit Records (Layer 3)
-- =============================================
IF OBJECT_ID('dbo.tr_acc_yearly_audit', 'U') IS NOT NULL
    DROP TABLE dbo.tr_acc_yearly_audit;
GO

CREATE TABLE dbo.tr_acc_yearly_audit (
    -- Primary Key
    yearly_audit_id INT IDENTITY(1,1) NOT NULL,
    
    -- Versioning Fields
    version_number INT NOT NULL DEFAULT 1,
    version_status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
    
    -- Period Information
    audit_year INT NOT NULL,
    audit_date DATE NOT NULL,
    
    -- COA Hierarchy
    coa_main_id INT NULL,
    coasub1_id INT NULL,
    coasub2_id INT NULL,
    coa_id INT NULL,
    
    -- Audit Balance Fields
    saldo_awal DECIMAL(18,2) NOT NULL DEFAULT 0,
    saldo_akhir DECIMAL(18,2) NOT NULL DEFAULT 0,
    adjustment_debet DECIMAL(18,2) NOT NULL DEFAULT 0,
    adjustment_kredit DECIMAL(18,2) NOT NULL DEFAULT 0,
    
    -- Audit Information
    audit_notes NVARCHAR(MAX) NULL,
    audit_findings NVARCHAR(MAX) NULL,
    audit_status VARCHAR(50) NULL, -- PENDING, IN_PROGRESS, COMPLETED, APPROVED
    
    -- Reference to Yearly Closing
    yearly_closing_id INT NULL,
    
    -- Audit Fields
    created_by VARCHAR(100) NULL,
    created_date DATETIME NOT NULL DEFAULT GETDATE(),
    modified_by VARCHAR(100) NULL,
    modified_date DATETIME NULL,
    approved_by VARCHAR(100) NULL,
    approved_date DATETIME NULL,
    auditor_name VARCHAR(100) NULL,
    
    -- Record Status
    rec_status CHAR(1) NOT NULL DEFAULT '1',
    
    -- Constraints
    CONSTRAINT PK_yearly_audit PRIMARY KEY (yearly_audit_id),
    CONSTRAINT CHK_yearly_audit_status CHECK (version_status IN ('DRAFT', 'ACTIVE', 'SUPERSEDED', 'ARCHIVED')),
    CONSTRAINT CHK_yearly_audit_rec_status CHECK (rec_status IN ('0', '1'))
);
GO

-- Index for performance
CREATE INDEX IX_yearly_audit_year ON dbo.tr_acc_yearly_audit(audit_year);
CREATE INDEX IX_yearly_audit_version ON dbo.tr_acc_yearly_audit(version_status);
CREATE INDEX IX_yearly_audit_coa ON dbo.tr_acc_yearly_audit(coa_main_id, coasub1_id, coasub2_id, coa_id);
GO

-- =============================================
-- Table 4: tr_acc_total_audit
-- Total Audit Records (Layer 4 - Final Archive)
-- =============================================
IF OBJECT_ID('dbo.tr_acc_total_audit', 'U') IS NOT NULL
    DROP TABLE dbo.tr_acc_total_audit;
GO

CREATE TABLE dbo.tr_acc_total_audit (
    -- Primary Key
    total_audit_id INT IDENTITY(1,1) NOT NULL,
    
    -- Versioning Fields
    version_number INT NOT NULL DEFAULT 1,
    version_status VARCHAR(20) NOT NULL DEFAULT 'DRAFT',
    
    -- Period Information
    audit_period_start DATE NOT NULL,
    audit_period_end DATE NOT NULL,
    audit_date DATE NOT NULL,
    
    -- COA Hierarchy
    coa_main_id INT NULL,
    coasub1_id INT NULL,
    coasub2_id INT NULL,
    coa_id INT NULL,
    
    -- Final Audit Balance
    opening_balance DECIMAL(18,2) NOT NULL DEFAULT 0,
    closing_balance DECIMAL(18,2) NOT NULL DEFAULT 0,
    total_adjustments DECIMAL(18,2) NOT NULL DEFAULT 0,
    
    -- Comprehensive Audit Information
    audit_summary NVARCHAR(MAX) NULL,
    compliance_status VARCHAR(50) NULL, -- COMPLIANT, NON_COMPLIANT, CONDITIONAL
    final_approval VARCHAR(50) NULL, -- APPROVED, REJECTED, PENDING
    
    -- References
    yearly_audit_id INT NULL,
    yearly_closing_id INT NULL,
    
    -- Archive Information
    archived_by VARCHAR(100) NULL,
    archived_date DATETIME NULL,
    
    -- Audit Fields
    created_by VARCHAR(100) NULL,
    created_date DATETIME NOT NULL DEFAULT GETDATE(),
    modified_by VARCHAR(100) NULL,
    modified_date DATETIME NULL,
    approved_by VARCHAR(100) NULL,
    approved_date DATETIME NULL,
    
    -- Record Status
    rec_status CHAR(1) NOT NULL DEFAULT '1',
    
    -- Constraints
    CONSTRAINT PK_total_audit PRIMARY KEY (total_audit_id),
    CONSTRAINT CHK_total_audit_status CHECK (version_status IN ('DRAFT', 'ACTIVE', 'SUPERSEDED', 'ARCHIVED')),
    CONSTRAINT CHK_total_audit_rec_status CHECK (rec_status IN ('0', '1'))
);
GO

-- Index for performance
CREATE INDEX IX_total_audit_period ON dbo.tr_acc_total_audit(audit_period_start, audit_period_end);
CREATE INDEX IX_total_audit_version ON dbo.tr_acc_total_audit(version_status);
CREATE INDEX IX_total_audit_coa ON dbo.tr_acc_total_audit(coa_main_id, coasub1_id, coasub2_id, coa_id);
GO

-- =============================================
-- Insert Sample Data for Testing
-- =============================================

-- Sample Monthly Closing (November 2025)
INSERT INTO dbo.tr_acc_monthly_closing 
    (version_number, version_status, closing_year, closing_month, closing_date, 
     coa_main_id, saldo_awal, saldo_akhir, mutasi_debet, mutasi_kredit,
     created_by, rec_status)
VALUES 
    (1, 'ACTIVE', 2025, 11, '2025-11-30', 1, 1000000.00, 1500000.00, 500000.00, 0.00, 'SYSTEM', '1'),
    (1, 'ACTIVE', 2025, 11, '2025-11-30', 2, 500000.00, 300000.00, 0.00, 200000.00, 'SYSTEM', '1'),
    (1, 'ACTIVE', 2025, 11, '2025-11-30', 3, 2000000.00, 2000000.00, 0.00, 0.00, 'SYSTEM', '1');
GO

-- Sample Yearly Closing (2025)
INSERT INTO dbo.tr_acc_yearly_closing
    (version_number, version_status, closing_year, closing_date,
     coa_main_id, saldo_awal, saldo_akhir, total_debet, total_kredit,
     created_by, rec_status)
VALUES
    (1, 'DRAFT', 2025, '2025-12-31', 1, 1000000.00, 1500000.00, 500000.00, 0.00, 'SYSTEM', '1'),
    (1, 'DRAFT', 2025, '2025-12-31', 2, 500000.00, 300000.00, 0.00, 200000.00, 'SYSTEM', '1');
GO

PRINT 'All closing tables created successfully!';
PRINT 'Tables created:';
PRINT '  1. tr_acc_monthly_closing';
PRINT '  2. tr_acc_yearly_closing';
PRINT '  3. tr_acc_yearly_audit';
PRINT '  4. tr_acc_total_audit';
PRINT '';
PRINT 'Sample data inserted for testing.';
GO
