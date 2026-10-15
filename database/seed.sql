-- Bagicha 2.0 Seed Data
USE `bagicha_db`;

-- Admin & Sample User (password for both is 'Password123')
-- Hash generated using PASSWORD_BCRYPT for 'Password123'
-- Admin & Sample Users (password for all is 'Password123')
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `role`) VALUES
(1, 'Bagicha Admin', 'admin@bagicha.com', '$2y$10$cZzlyliISmQ9bD5cds4tfe2n2VfMFz0rL/Q8YOol/y2H2Ihg1CXdO', '9820012345', 'admin'),
(2, 'Priya Sharma', 'priya.sharma@bagicha.com', '$2y$10$cZzlyliISmQ9bD5cds4tfe2n2VfMFz0rL/Q8YOol/y2H2Ihg1CXdO', '9820154321', 'customer'),
(3, 'Rohan Mehta', 'rohan.mehta@bagicha.com', '$2y$10$cZzlyliISmQ9bD5cds4tfe2n2VfMFz0rL/Q8YOol/y2H2Ihg1CXdO', '9811267890', 'customer'),
(4, 'Ananya Iyer', 'ananya.iyer@bagicha.com', '$2y$10$cZzlyliISmQ9bD5cds4tfe2n2VfMFz0rL/Q8YOol/y2H2Ihg1CXdO', '9871123456', 'customer'),
(5, 'Vikramaditya Rao', 'vikram.rao@bagicha.com', '$2y$10$cZzlyliISmQ9bD5cds4tfe2n2VfMFz0rL/Q8YOol/y2H2Ihg1CXdO', '9930087654', 'customer'),
(6, 'Sneha Kapoor', 'sneha.kapoor@bagicha.com', '$2y$10$cZzlyliISmQ9bD5cds4tfe2n2VfMFz0rL/Q8YOol/y2H2Ihg1CXdO', '9899011223', 'customer'),
(7, 'Arjun Nair', 'arjun.nair@bagicha.com', '$2y$10$cZzlyliISmQ9bD5cds4tfe2n2VfMFz0rL/Q8YOol/y2H2Ihg1CXdO', '9717033445', 'customer')
ON DUPLICATE KEY UPDATE `full_name`=VALUES(`full_name`), `password`=VALUES(`password`);

-- Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `is_featured`) VALUES
(1, 'Indoor Plants', 'indoor-plants', 'Air-purifying & lush indoor greens perfect for rooms and offices.', 'https://images.unsplash.com/photo-1614594975525-e45190c55d0b?auto=format&fit=crop&w=600&q=80', 1),
(2, 'Outdoor Plants', 'outdoor-plants', 'Sun-loving shrubs and hardy garden plants.', 'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?auto=format&fit=crop&w=600&q=80', 1),
(3, 'Flowering Plants', 'flowering-plants', 'Vibrant blooming plants to bring color to your balcony.', 'https://images.unsplash.com/photo-1597848212624-a19eb35e2651?auto=format&fit=crop&w=600&q=80', 1),
(4, 'Succulents & Cacti', 'succulents-cacti', 'Low-maintenance desert beauties for desk decor.', 'https://images.unsplash.com/photo-1509423350716-97f9360b4e09?auto=format&fit=crop&w=600&q=80', 1),
(5, 'Gardening Tools', 'gardening-tools', 'Premium shears, trowels, sprayers & watering cans.', 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=600&q=80', 0),
(6, 'Seeds & Fertilizers', 'seeds-fertilizers', 'Organic soil, plant food, and high-yield seeds.', 'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?auto=format&fit=crop&w=600&q=80', 0)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Products
INSERT INTO `products` 
(`id`, `category_id`, `name`, `slug`, `short_description`, `description`, `price`, `sale_price`, `stock_quantity`, `sku`, `main_image`, `light_requirement`, `water_requirement`, `maintenance_level`, `is_pet_friendly`, `is_featured`, `rating_avg`, `rating_count`) 
VALUES
(1, 1, 'Monstera Deliciosa (Swiss Cheese Plant)', 'monstera-deliciosa', 'Iconic tropical indoor plant with signature split leaves.', 'The Monstera Deliciosa is a stunning tropical houseplant known for its large, glossy green leaves with natural holes (fenestrations). Thrives in bright indirect light and adds instant jungle vibes to any space.', 1299.00, 999.00, 25, 'PLT-MON-01', 'https://images.unsplash.com/photo-1614594975525-e45190c55d0b?auto=format&fit=crop&w=800&q=80', 'Bright Indirect', 'Once a week', 'Medium', 0, 1, 4.85, 34),
(2, 1, 'Snake Plant (Sansevieria Trifasciata)', 'snake-plant', 'Tough air-purifying plant that survives low light & neglect.', 'Snake Plant is one of the easiest house plants to maintain. It purifies indoor air by removing toxins like benzene and formaldehyde, making it ideal for bedrooms and workspaces.', 699.00, 549.00, 40, 'PLT-SNK-01', 'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?auto=format&fit=crop&w=800&q=80', 'Low to Bright', 'Every 2-3 weeks', 'Low', 0, 1, 4.90, 52),
(3, 1, 'Peace Lily (Spathiphyllum)', 'peace-lily', 'Elegant dark green leaves with graceful white flower blooms.', 'Peace Lily brings tranquility to your home while acting as an exceptional natural air cleanser. It lets you know when it needs water by gently drooping its leaves.', 799.00, 649.00, 30, 'PLT-PCL-01', 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?auto=format&fit=crop&w=800&q=80', 'Medium Shade', 'Twice a week', 'Medium', 0, 1, 4.70, 18),
(4, 4, 'Echeveria Elegans Succulent', 'echeveria-elegans', 'Compact rosette succulent with silver-blue fleshy leaves.', 'Echeveria Elegans, also known as Mexican Snow Ball, features charming compact rosettes. Perfect for sunny windowsill placement or dish garden arrangements.', 349.00, 299.00, 50, 'PLT-ECH-01', 'https://images.unsplash.com/photo-1509423350716-97f9360b4e09?auto=format&fit=crop&w=800&q=80', 'Direct Sunlight', 'Sparse / Monthly', 'Low', 1, 1, 4.95, 29),
(5, 3, 'Bougainvillea Pink Blossom', 'bougainvillea-pink', 'Vibrant outdoor climber overflowing with magenta blooms.', 'Bougainvillea is a hardy sun lover that thrives in warm climates. Its intense pink papery bracts produce spectacular floral displays throughout the year.', 899.00, 749.00, 15, 'PLT-BOU-01', 'https://images.unsplash.com/photo-1597848212624-a19eb35e2651?auto=format&fit=crop&w=800&q=80', 'Full Sunlight', 'Daily', 'Medium', 1, 0, 4.60, 12),
(6, 5, 'Ergonomic Japanese Pruning Shears', 'japanese-pruning-shears', 'High-carbon stainless steel bypass pruners for clean cuts.', 'Crafted for precise clean cuts without damaging stems. Features lightweight ergonomic handles and a safety lock system for effortless gardening.', 1199.00, 999.00, 20, 'TL-SHR-01', 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=800&q=80', 'N/A', 'N/A', 'Low', 1, 0, 4.80, 15),
(7, 6, 'Organic Neem Oil Fertilizer Spray (500ml)', 'neem-oil-spray', '100% natural cold-pressed pesticide and plant immunity booster.', 'Keep your plants healthy without harsh chemicals. Neem oil controls aphids, mealybugs, and spider mites while feeding soil microbes naturally.', 449.00, 399.00, 60, 'FERT-NEM-01', 'https://images.unsplash.com/photo-1523348837708-15d4a09cfac2?auto=format&fit=crop&w=800&q=80', 'N/A', 'N/A', 'Low', 1, 0, 4.75, 41),
(8, 2, 'Areca Palm (Chrysalidocarpus Lutescens)', 'areca-palm', 'Lush, feathery tropical outdoor and patio palm that brings island vibes.', 'The Areca Palm is a popular outdoor and balcony plant known for its bright green, arching fronds. Thrives in bright sunlight, perfect for garden landscaping, terraces, or shaded outdoor patios.', 999.00, 799.00, 35, 'PLT-ARC-01', 'https://images.unsplash.com/photo-1545241047-6083a3684587?auto=format&fit=crop&w=800&q=80', 'Bright Direct', '2-3 times a week', 'Low', 1, 1, 4.80, 24),
(9, 2, 'Hibiscus Red Radiance', 'hibiscus-red-radiance', 'Vibrant tropical flowering outdoor shrub with bold crimson blossoms.', 'Hibiscus Red Radiance is a fast-growing outdoor flowering plant that produces massive, vibrant red blooms. Needs full sunlight and regular watering to bloom continuously throughout the summer.', 549.00, 449.00, 45, 'PLT-HIB-01', 'https://images.unsplash.com/photo-1550950158-d0d960dff51b?auto=format&fit=crop&w=800&q=80', 'Full Sunlight', 'Daily', 'Medium', 1, 1, 4.75, 19),
(10, 2, 'Golden Bamboo (Phyllostachys Aureosulcata)', 'golden-bamboo', 'Fast-growing privacy screen plant with elegant golden canes.', 'Golden Bamboo provides natural garden privacy, lush screening, and rustling tropical foliage. Highly resilient outdoor ornamental plant ideal for borders, hedges, or large planters.', 1499.00, 1199.00, 20, 'PLT-BMB-01', 'https://images.unsplash.com/photo-1516205651411-aef33a44f7c2?auto=format&fit=crop&w=800&q=80', 'Full Sunlight', 'Twice a week', 'Low', 1, 0, 4.90, 15),
(11, 2, 'Ficus Benjamina (Weeping Fig)', 'ficus-benjamina-outdoor', 'Graceful evergreen outdoor tree with glossy dense green leaves.', 'Ficus Benjamina is a classic outdoor garden foliage plant featuring dense, graceful foliage and slender stems. Excellent for garden lawns, entryways, and courtyard styling.', 899.00, 699.00, 28, 'PLT-FIC-01', 'https://images.unsplash.com/photo-1599598425947-2206763844d2?auto=format&fit=crop&w=800&q=80', 'Bright Direct', '2 times a week', 'Medium', 0, 1, 4.65, 11),
(12, 2, 'Jasmine Madurai Malli (Jasminum Sambac)', 'jasmine-madurai-malli', 'Intensely fragrant white floral climber for balconies & garden trellises.', 'Famous for its intoxicating sweet scent, Madurai Jasmine features star-shaped white flowers blooming abundantly in full sun. Great for balcony railings, garden trellis, and pots.', 499.00, 399.00, 50, 'PLT-JAS-01', 'https://images.unsplash.com/photo-1508610048659-a06b669e3321?auto=format&fit=crop&w=800&q=80', 'Full Sunlight', 'Daily', 'Low', 1, 1, 4.95, 38)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Product Images Gallery
INSERT INTO `product_images` (`product_id`, `image_url`, `sort_order`) VALUES
(1, 'https://images.unsplash.com/photo-1614594975525-e45190c55d0b?auto=format&fit=crop&w=800&q=80', 1),
(1, 'https://images.unsplash.com/photo-1545241047-6083a3684587?auto=format&fit=crop&w=800&q=80', 2),
(2, 'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?auto=format&fit=crop&w=800&q=80', 1),
(3, 'https://images.unsplash.com/photo-1485955900006-10f4d324d411?auto=format&fit=crop&w=800&q=80', 1),
(8, 'https://images.unsplash.com/photo-1545241047-6083a3684587?auto=format&fit=crop&w=800&q=80', 1),
(9, 'https://images.unsplash.com/photo-1550950158-d0d960dff51b?auto=format&fit=crop&w=800&q=80', 1),
(10, 'https://images.unsplash.com/photo-1516205651411-aef33a44f7c2?auto=format&fit=crop&w=800&q=80', 1),
(11, 'https://images.unsplash.com/photo-1599598425947-2206763844d2?auto=format&fit=crop&w=800&q=80', 1),
(12, 'https://images.unsplash.com/photo-1508610048659-a06b669e3321?auto=format&fit=crop&w=800&q=80', 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Discount Coupons
INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_amount`, `min_order_amount`, `is_active`) VALUES
(1, 'WELCOME10', 'percentage', 10.00, 499.00, 1),
(2, 'SUMMER200', 'fixed', 200.00, 999.00, 1),
(3, 'MONSTERA15', 'percentage', 15.00, 799.00, 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Sample Customer Orders
INSERT INTO `orders` (`id`, `order_number`, `user_id`, `total_amount`, `discount_amount`, `final_amount`, `order_status`, `payment_status`, `payment_method`, `shipping_address`) VALUES
(1, 'BAG-1001', 2, 1548.00, 0.00, 1548.00, 'delivered', 'paid', 'razorpay', '123 Green Avenue, Flat 4B, Sector 15, New Delhi - 110001'),
(2, 'BAG-1002', 3, 1247.00, 100.00, 1147.00, 'shipped', 'paid', 'razorpay', '45 Marine Drive, Penthouse 8, Mumbai - 400020'),
(3, 'BAG-1003', 4, 749.00, 0.00, 749.00, 'processing', 'paid', 'cod', '88 Indiranagar, 10th Main, Bengaluru - 560038')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Sample Order Items
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `subtotal`) VALUES
(1, 1, 1, 'Monstera Deliciosa (Swiss Cheese Plant)', 999.00, 1, 999.00),
(2, 1, 2, 'Snake Plant (Sansevieria Trifasciata)', 549.00, 1, 549.00),
(3, 2, 3, 'Peace Lily (Spathiphyllum)', 649.00, 1, 649.00),
(4, 2, 4, 'Echeveria Elegans Succulent', 299.00, 2, 598.00),
(5, 3, 5, 'Bougainvillea Pink Blossom', 749.00, 1, 749.00)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Sample Customer Product Reviews (Reflected dynamically on storefront & Admin panel)
INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `is_approved`) VALUES
(1, 1, 2, 5, 'Ordered a Monstera Deliciosa and Snake Plant. Both arrived in incredibly secure packaging without a single torn leaf! 2 months later they\'re thriving.', 1),
(2, 3, 3, 5, 'The free plant care guidance via chat saved my Fiddle Leaf Fig! Super prompt customer support and premium organic fertilizers.', 1),
(3, 2, 4, 5, 'Bagicha has transformed my balcony into a lush green haven. Fast delivery, healthy root systems, and gorgeous ceramic pots!', 1),
(4, 4, 5, 5, 'Echeveria arrived plump, hydrated, and compact. Perfect accent for my work studio desk setup in Hyderabad.', 1),
(5, 7, 6, 5, 'The organic neem oil spray cleared spider mites off my balcony plants within 3 days. Super effective natural remedy!', 1),
(6, 6, 7, 4, 'Japanese pruning shears feel solid in hand and give ultra-clean cuts without tearing young branches.', 1)
ON DUPLICATE KEY UPDATE `comment`=VALUES(`comment`);

-- Store Settings Seeds
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('store_name', 'Bagicha'),
('store_tagline', 'Bring Nature Home'),
('contact_email', 'support@bagicha.com'),
('contact_phone', '+91 1800 224 4242'),
('free_shipping_threshold', '999.00'),
('flat_shipping_rate', '99.00'),
('announcement_banner', '🌿 Free Eco-Friendly Delivery on all plant orders above ₹999!')
ON DUPLICATE KEY UPDATE `setting_value`=VALUES(`setting_value`);

