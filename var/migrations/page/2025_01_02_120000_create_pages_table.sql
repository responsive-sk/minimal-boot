-- Migration: create_pages_table
-- Created: 2025-01-02 12:00:00

-- Create pages table for static page management
CREATE TABLE pages (
    id VARCHAR(255) PRIMARY KEY,
    slug VARCHAR(255) NOT NULL UNIQUE,
    title VARCHAR(500) NOT NULL,
    content TEXT NOT NULL,
    meta_description TEXT DEFAULT '',
    meta_keywords TEXT DEFAULT '',
    is_published BOOLEAN DEFAULT 0,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NULL
);

-- Create index for faster slug lookups
CREATE INDEX idx_pages_slug ON pages(slug);

-- Create index for published pages
CREATE INDEX idx_pages_published ON pages(is_published, created_at);

-- Insert sample pages
INSERT INTO pages (id, slug, title, content, meta_description, is_published, created_at) VALUES
('page_about', 'about', 'About Us', 
 '<h1>About Minimal Boot</h1>
  <p>Minimal Boot is a lightweight, PSR-15 compliant web application framework built on top of Mezzio. It follows Domain-Driven Design principles and provides a clean, modular architecture for building modern web applications.</p>
  
  <h2>Key Features</h2>
  <ul>
    <li><strong>Domain-Driven Design</strong> - Clean separation of concerns with Domain, Application, and Infrastructure layers</li>
    <li><strong>Modular Architecture</strong> - Self-contained modules with their own handlers, templates, and services</li>
    <li><strong>Native PHP Templates</strong> - No external template engine dependencies for better performance</li>
    <li><strong>PSR-15 Middleware</strong> - Full PSR-15 compliance for HTTP message handling</li>
    <li><strong>Repository Pattern</strong> - Abstracted data access with interface-based design</li>
    <li><strong>Code Quality</strong> - PHPStan Level 8 and PSR-12 code standards</li>
  </ul>
  
  <h2>Architecture</h2>
  <p>The framework implements a clean architecture with clear separation between:</p>
  <ul>
    <li><strong>Domain Layer</strong> - Business logic, entities, and domain services</li>
    <li><strong>Application Layer</strong> - Use cases and application orchestration</li>
    <li><strong>Infrastructure Layer</strong> - Database access, external services, and technical concerns</li>
    <li><strong>Presentation Layer</strong> - HTTP handlers, templates, and user interface</li>
  </ul>', 
 'Learn about Minimal Boot framework, its architecture, and key features for modern web development.',
 1, 
 CURRENT_TIMESTAMP),

('page_privacy', 'privacy', 'Privacy Policy', 
 '<h1>Privacy Policy</h1>
  <p>This privacy policy explains how we collect, use, and protect your personal information when you use our website.</p>
  
  <h2>Information We Collect</h2>
  <p>We may collect the following types of information:</p>
  <ul>
    <li>Contact information (name, email address) when you submit forms</li>
    <li>Usage data and analytics to improve our website</li>
    <li>Technical information such as IP address and browser type</li>
  </ul>
  
  <h2>How We Use Your Information</h2>
  <p>We use your information to:</p>
  <ul>
    <li>Respond to your inquiries and requests</li>
    <li>Improve our website and services</li>
    <li>Send you updates if you have opted in</li>
  </ul>
  
  <h2>Data Protection</h2>
  <p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>
  
  <h2>Contact Us</h2>
  <p>If you have any questions about this privacy policy, please contact us through our contact form.</p>', 
 'Privacy policy explaining how we collect, use, and protect your personal information.',
 1, 
 CURRENT_TIMESTAMP),

('page_terms', 'terms', 'Terms of Service', 
 '<h1>Terms of Service</h1>
  <p>By using this website, you agree to comply with and be bound by the following terms and conditions.</p>
  
  <h2>Use License</h2>
  <p>Permission is granted to temporarily download one copy of the materials on this website for personal, non-commercial transitory viewing only.</p>
  
  <h2>Disclaimer</h2>
  <p>The materials on this website are provided on an "as is" basis. We make no warranties, expressed or implied, and hereby disclaim and negate all other warranties including without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</p>
  
  <h2>Limitations</h2>
  <p>In no event shall our company or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on this website.</p>
  
  <h2>Modifications</h2>
  <p>We may revise these terms of service at any time without notice. By using this website, you are agreeing to be bound by the then current version of these terms of service.</p>', 
 'Terms of service and conditions for using our website and services.',
 1, 
 CURRENT_TIMESTAMP);
