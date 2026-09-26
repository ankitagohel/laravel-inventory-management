<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with electronics & home appliances in INR (₹).
     */
    public function run(): void
    {
        // 1. Create Default Users for Each Role
        User::updateOrCreate(
            ['email' => 'admin@inventory.local'],
            [
                'name' => 'Alexander Vance (Admin)',
                'role' => User::ROLE_ADMIN,
                'password' => bcrypt('password123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'manager@inventory.local'],
            [
                'name' => 'Rachel Hayes (Manager)',
                'role' => User::ROLE_MANAGER,
                'password' => bcrypt('password123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'staff@inventory.local'],
            [
                'name' => 'Leo Castillo (Staff)',
                'role' => User::ROLE_STAFF,
                'password' => bcrypt('password123'),
            ]
        );

        User::updateOrCreate(
            ['email' => 'auditor@inventory.local'],
            [
                'name' => 'Sophia Morales (Auditor)',
                'role' => User::ROLE_AUDITOR,
                'password' => bcrypt('password123'),
            ]
        );

        // 2. Electronics & Appliance Categories
        $categoriesData = [
            [
                'name' => 'Refrigerators & Freezers',
                'code' => 'REF',
                'description' => 'French door, double door, frost-free, and smart convertible refrigerators',
            ],
            [
                'name' => 'Washing Machines & Dryers',
                'code' => 'WASH',
                'description' => 'Front load, top load fully automatic washers, and dryer combos',
            ],
            [
                'name' => 'Televisions & Home Audio',
                'code' => 'TV',
                'description' => '4K OLED, QLED, Smart Google TVs, soundbars, and cinema displays',
            ],
            [
                'name' => 'Laptops & Computers',
                'code' => 'LAP',
                'description' => 'Ultrabooks, gaming notebooks, business laptops, and workstation PCs',
            ],
            [
                'name' => 'Air Conditioners & Cooling',
                'code' => 'AC',
                'description' => 'Inverter split ACs, window cooling units, and smart climate control',
            ],
            [
                'name' => 'Kitchen & Cooking Appliances',
                'code' => 'KIT',
                'description' => 'Convection microwaves, digital air fryers, induction hobs, and dishwashers',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['code']] = Category::firstOrCreate(['name' => $cat['name']], $cat);
        }

        // 3. Indian Electronics Suppliers & Brand Distributors
        $suppliersData = [
            [
                'name' => 'Samsung India Electronics Pvt Ltd',
                'contact_person' => 'Rajesh Sharma',
                'email' => 'b2b.orders@samsung.com',
                'phone' => '+91 124 488 1234',
                'address' => 'Two Horizon Center, Golf Course Road, DLF Phase 5, Gurugram, Haryana',
            ],
            [
                'name' => 'LG Electronics India Pvt Ltd',
                'contact_person' => 'Amitabh Sen',
                'email' => 'commercial.sales@lge.com',
                'phone' => '+91 120 256 0900',
                'address' => 'Plot No. 51, Surajpur Kasna Road, Greater Noida, Uttar Pradesh',
            ],
            [
                'name' => 'Sony India Pvt Ltd',
                'contact_person' => 'Vikram Malhotra',
                'email' => 'bravia.distributors@sony.co.in',
                'phone' => '+91 11 6608 9000',
                'address' => 'A-18, Mohan Co-operative Industrial Estate, Mathura Road, New Delhi',
            ],
            [
                'name' => 'BSH Home Appliances Pvt Ltd (Bosch)',
                'contact_person' => 'Ananya Deshmukh',
                'email' => 'orders.india@bshg.com',
                'phone' => '+91 22 6751 8000',
                'address' => 'Arena House, 2nd Floor, Main Building, Plot No. 103, Road No. 12, MIDC, Andheri East, Mumbai',
            ],
            [
                'name' => 'Apple India Pvt Ltd',
                'contact_person' => 'Karan Singhania',
                'email' => 'india_enterprise@apple.com',
                'phone' => '+91 80 4045 5155',
                'address' => '19th Floor, Concorde Tower C, UB City, No.24, Vittal Mallya Road, Bengaluru, Karnataka',
            ],
            [
                'name' => 'Whirlpool of India Ltd',
                'contact_person' => 'Suresh Verma',
                'email' => 'sales.north@whirlpool.com',
                'phone' => '+91 124 459 1300',
                'address' => 'Whirlpool House, Plot No. 40, Sector 44, Gurugram, Haryana',
            ],
        ];

        $suppliers = [];
        foreach ($suppliersData as $sup) {
            $suppliers[$sup['name']] = Supplier::firstOrCreate(['name' => $sup['name']], $sup);
        }

        // 4. Electronics & Appliance Products Catalog in INR (₹)
        $productsData = [
            // --- REFRIGERATORS ---
            [
                'sku' => 'REF-SAM-580L',
                'barcode' => '890103010101',
                'name' => 'Samsung 580L French Door Smart Refrigerator (Family Hub)',
                'description' => 'Triple Cooling system, built-in touchscreen display, beverage center, dual auto ice maker, matte black finish.',
                'category_code' => 'REF',
                'supplier_name' => 'Samsung India Electronics Pvt Ltd',
                'cost_price' => 165000.00,
                'selling_price' => 219990.00,
                'current_stock' => 8,
                'min_stock_alert' => 4,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay R-01 (Heavy Appliance)',
            ],
            [
                'sku' => 'REF-LG-420L',
                'barcode' => '890103010102',
                'name' => 'LG 420L Frost-Free Double Door Smart Inverter Fridge',
                'description' => 'DoorCooling+, Smart Inverter Compressor, Multi Air Flow, Convertible Freezer, Shiny Steel.',
                'category_code' => 'REF',
                'supplier_name' => 'LG Electronics India Pvt Ltd',
                'cost_price' => 48000.00,
                'selling_price' => 64990.00,
                'current_stock' => 14,
                'min_stock_alert' => 5,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay R-03 (Appliance Row 1)',
            ],
            [
                'sku' => 'REF-WHP-265L',
                'barcode' => '890103010103',
                'name' => 'Whirlpool 265L 3-Door Multi-Door Refrigerator (Proton)',
                'description' => 'Active Fresh Zone, Moisture Retention Technology, Air Booster, German Steel Silver finish.',
                'category_code' => 'REF',
                'supplier_name' => 'Whirlpool of India Ltd',
                'cost_price' => 28500.00,
                'selling_price' => 36990.00,
                'current_stock' => 2, // LOW STOCK ALERT
                'min_stock_alert' => 5,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay R-05 (Appliance Row 2)',
            ],
            [
                'sku' => 'REF-BOSCH-600L',
                'barcode' => '890103010104',
                'name' => 'Bosch Series 6 605L Cross Door Freestanding Refrigerator',
                'description' => 'VitaFresh Pro freshness system, MultiAirflow cooling, Home Connect Wi-Fi, anti-fingerprint stainless steel.',
                'category_code' => 'REF',
                'supplier_name' => 'BSH Home Appliances Pvt Ltd (Bosch)',
                'cost_price' => 115000.00,
                'selling_price' => 149990.00,
                'current_stock' => 0, // OUT OF STOCK TRIGGER
                'min_stock_alert' => 3,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay R-02 (Heavy Appliance)',
            ],

            // --- WASHING MACHINES ---
            [
                'sku' => 'WASH-BSH-8KG',
                'barcode' => '890103010105',
                'name' => 'Bosch Series 6 8kg 1400 RPM Front Load Washing Machine',
                'description' => 'EcoSilence Drive brushless motor, ActiveWater Plus pressure sensor, AllergyPlus program, anti-vibration sidewalls.',
                'category_code' => 'WASH',
                'supplier_name' => 'BSH Home Appliances Pvt Ltd (Bosch)',
                'cost_price' => 39000.00,
                'selling_price' => 51990.00,
                'current_stock' => 11,
                'min_stock_alert' => 4,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay W-01 (Washers)',
            ],
            [
                'sku' => 'WASH-LG-9KG',
                'barcode' => '890103010106',
                'name' => 'LG 9kg AI Direct Drive Front Load Steam Washer',
                'description' => 'AI DD fabric sensor, Steam+ allergy care, TurboWash 360 (39 mins cycle), ThinQ Wi-Fi remote control, Platinum Black.',
                'category_code' => 'WASH',
                'supplier_name' => 'LG Electronics India Pvt Ltd',
                'cost_price' => 44000.00,
                'selling_price' => 58990.00,
                'current_stock' => 9,
                'min_stock_alert' => 4,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay W-02 (Washers)',
            ],
            [
                'sku' => 'WASH-SAM-75KG',
                'barcode' => '890103010107',
                'name' => 'Samsung 7.5kg EcoBubble Top Load Automatic Washer',
                'description' => 'BubbleStorm technology, Dual Storm pulsator, Digital Inverter motor with 20-year warranty, soft close tempered glass.',
                'category_code' => 'WASH',
                'supplier_name' => 'Samsung India Electronics Pvt Ltd',
                'cost_price' => 21500.00,
                'selling_price' => 29990.00,
                'current_stock' => 3, // LOW STOCK ALERT
                'min_stock_alert' => 6,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay W-04 (Top Loaders)',
            ],
            [
                'sku' => 'WASH-WHP-10KG',
                'barcode' => '890103010108',
                'name' => 'Whirlpool 10.5kg 360 BloomWash Pro Top Load Washer',
                'description' => 'Built-in heater with 6th Sense Stainwash, catalytic soak, zero pressure fill technology, graphite grey.',
                'category_code' => 'WASH',
                'supplier_name' => 'Whirlpool of India Ltd',
                'cost_price' => 27000.00,
                'selling_price' => 37990.00,
                'current_stock' => 7,
                'min_stock_alert' => 3,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay W-03 (Top Loaders)',
            ],

            // --- TELEVISIONS ---
            [
                'sku' => 'TV-SONY-65OLED',
                'barcode' => '890103010109',
                'name' => 'Sony Bravia XR 65" 4K HDR OLED Google TV (A80L)',
                'description' => 'Cognitive Processor XR, Acoustic Surface Audio+, XR OLED Contrast Pro, 120Hz gaming, HDMI 2.1 VRR, IMAX Enhanced.',
                'category_code' => 'TV',
                'supplier_name' => 'Sony India Pvt Ltd',
                'cost_price' => 175000.00,
                'selling_price' => 229990.00,
                'current_stock' => 6,
                'min_stock_alert' => 3,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay TV-01 (OLED Bay)',
            ],
            [
                'sku' => 'TV-SAM-55QLED',
                'barcode' => '890103010110',
                'name' => 'Samsung 55" Neo QLED 4K Smart TV (QN85D)',
                'description' => 'NQ4 AI Gen2 Processor, Quantum Matrix Mini LED, Dolby Atmos, Real Depth Enhancer, Motion Xcelerator 120Hz.',
                'category_code' => 'TV',
                'supplier_name' => 'Samsung India Electronics Pvt Ltd',
                'cost_price' => 78000.00,
                'selling_price' => 104990.00,
                'current_stock' => 16,
                'min_stock_alert' => 5,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay TV-02 (QLED Displays)',
            ],
            [
                'sku' => 'TV-LG-65OLED',
                'barcode' => '890103010111',
                'name' => 'LG 65" 4K OLED evo Smart TV (C3 Series)',
                'description' => 'α9 AI Processor Gen6, Brightness Booster, Dolby Vision & Atmos, 0.1ms response time, NVIDIA G-Sync & FreeSync.',
                'category_code' => 'TV',
                'supplier_name' => 'LG Electronics India Pvt Ltd',
                'cost_price' => 145000.00,
                'selling_price' => 189990.00,
                'current_stock' => 4, // LOW STOCK ALERT
                'min_stock_alert' => 5,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay TV-03 (OLED Bay)',
            ],
            [
                'sku' => 'TV-SONY-50X75',
                'barcode' => '890103010112',
                'name' => 'Sony Bravia 50" 4K Ultra HD Smart LED TV (X75L)',
                'description' => 'X1 4K Processor, Live Color technology, Google TV with Voice Search, Open Baffle Speaker with Dolby Audio.',
                'category_code' => 'TV',
                'supplier_name' => 'Sony India Pvt Ltd',
                'cost_price' => 46000.00,
                'selling_price' => 59990.00,
                'current_stock' => 20,
                'min_stock_alert' => 6,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay TV-04 (LED Displays)',
            ],

            // --- LAPTOPS ---
            [
                'sku' => 'LAP-MBP-16M3',
                'barcode' => '890103010113',
                'name' => 'Apple MacBook Pro 16" M3 Max (36GB RAM, 1TB SSD)',
                'description' => '16-core CPU, 40-core GPU, Liquid Retina XDR display (1600 nits peak), Space Black, 22-hour battery life.',
                'category_code' => 'LAP',
                'supplier_name' => 'Apple India Pvt Ltd',
                'cost_price' => 295000.00,
                'selling_price' => 349900.00,
                'current_stock' => 7,
                'min_stock_alert' => 3,
                'unit' => 'pcs',
                'warehouse_location' => 'Vault L-01 (High Value)',
            ],
            [
                'sku' => 'LAP-MBA-15M3',
                'barcode' => '890103010114',
                'name' => 'Apple MacBook Air 15" M3 (16GB Unified RAM, 512GB SSD)',
                'description' => 'Liquid Retina display, fanless silent operation, 1080p FaceTime HD camera, MagSafe 3 charging, Midnight finish.',
                'category_code' => 'LAP',
                'supplier_name' => 'Apple India Pvt Ltd',
                'cost_price' => 118000.00,
                'selling_price' => 144900.00,
                'current_stock' => 15,
                'min_stock_alert' => 5,
                'unit' => 'pcs',
                'warehouse_location' => 'Vault L-02 (High Value)',
            ],
            [
                'sku' => 'LAP-DELL-XPS15',
                'barcode' => '890103010115',
                'name' => 'Dell XPS 15 9530 Core i9 (32GB RAM, 1TB SSD, RTX 4070)',
                'description' => '15.6" 3.5K OLED InfinityEdge touch display, CNC aluminum chassis, carbon fiber palm rest, studio sound quad speakers.',
                'category_code' => 'LAP',
                'supplier_name' => 'Samsung India Electronics Pvt Ltd',
                'cost_price' => 185000.00,
                'selling_price' => 239990.00,
                'current_stock' => 9,
                'min_stock_alert' => 4,
                'unit' => 'pcs',
                'warehouse_location' => 'Rack L-03 (High Performance)',
            ],
            [
                'sku' => 'LAP-LEN-X1C',
                'barcode' => '890103010116',
                'name' => 'Lenovo ThinkPad X1 Carbon Gen 12 (Intel Ultra 7, 32GB RAM)',
                'description' => 'Ultra-light 1.09kg magnesium chassis, 14" 2.8K OLED display, trackpoint keyboard, military-grade MIL-STD 810H durability.',
                'category_code' => 'LAP',
                'supplier_name' => 'Samsung India Electronics Pvt Ltd',
                'cost_price' => 155000.00,
                'selling_price' => 198990.00,
                'current_stock' => 8,
                'min_stock_alert' => 4,
                'unit' => 'pcs',
                'warehouse_location' => 'Rack L-04 (Business Laptops)',
            ],
            [
                'sku' => 'LAP-ASUS-ZEPH',
                'barcode' => '890103010117',
                'name' => 'ASUS ROG Zephyrus G16 OLED Gaming Laptop (RTX 4080)',
                'description' => 'Intel Core Ultra 9, 2.5K 240Hz ROG Nebula OLED display, slash lighting CNC aluminum lid, 32GB LPDDR5X, Eclipse Gray.',
                'category_code' => 'LAP',
                'supplier_name' => 'Samsung India Electronics Pvt Ltd',
                'cost_price' => 195000.00,
                'selling_price' => 249990.00,
                'current_stock' => 0, // OUT OF STOCK TRIGGER
                'min_stock_alert' => 3,
                'unit' => 'pcs',
                'warehouse_location' => 'Rack L-05 (Gaming Notebooks)',
            ],

            // --- AIR CONDITIONERS & KITCHEN APPLIANCES ---
            [
                'sku' => 'AC-DAIK-15T',
                'barcode' => '890103010118',
                'name' => 'Daikin 1.5 Ton 5-Star Triple Display Inverter Split AC',
                'description' => 'PM 2.5 filter, Dew clean technology, 3D airflow cooling, 100% copper condenser, stabilizer-free operation.',
                'category_code' => 'AC',
                'supplier_name' => 'LG Electronics India Pvt Ltd',
                'cost_price' => 36500.00,
                'selling_price' => 47990.00,
                'current_stock' => 18,
                'min_stock_alert' => 5,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay AC-01 (Air Conditioners)',
            ],
            [
                'sku' => 'KIT-PAN-32L',
                'barcode' => '890103010119',
                'name' => 'Panasonic 32L Convection Microwave Oven with 360 Grill',
                'description' => 'Inverter defrost technology, magic grill, zero-oil cooking modes, touch keypad, stainless steel interior cavity.',
                'category_code' => 'KIT',
                'supplier_name' => 'BSH Home Appliances Pvt Ltd (Bosch)',
                'cost_price' => 14200.00,
                'selling_price' => 19990.00,
                'current_stock' => 22,
                'min_stock_alert' => 6,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay K-01 (Small Appliances)',
            ],
            [
                'sku' => 'KIT-PHIL-XXL',
                'barcode' => '890103010120',
                'name' => 'Philips XXL Digital Smart Sensing Air Fryer 7.2L',
                'description' => 'Rapid CombiAir technology, auto-cook presets, food thermometer probe, fat removal technology, dishwasher-safe.',
                'category_code' => 'KIT',
                'supplier_name' => 'BSH Home Appliances Pvt Ltd (Bosch)',
                'cost_price' => 12500.00,
                'selling_price' => 16995.00,
                'current_stock' => 25,
                'min_stock_alert' => 8,
                'unit' => 'pcs',
                'warehouse_location' => 'Bay K-02 (Small Appliances)',
            ],
        ];

        foreach ($productsData as $item) {
            $cat = $categories[$item['category_code']] ?? null;
            $sup = $suppliers[$item['supplier_name']] ?? null;

            $product = Product::updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'barcode' => $item['barcode'],
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'category_id' => $cat?->id,
                    'supplier_id' => $sup?->id,
                    'cost_price' => $item['cost_price'],
                    'selling_price' => $item['selling_price'],
                    'current_stock' => $item['current_stock'],
                    'min_stock_alert' => $item['min_stock_alert'],
                    'unit' => $item['unit'],
                    'warehouse_location' => $item['warehouse_location'],
                    'status' => 'active',
                ]
            );

            // Record initial stock intake movement
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'IN',
                'quantity' => $item['current_stock'] + 8,
                'previous_stock' => 0,
                'resulting_stock' => $item['current_stock'] + 8,
                'reason' => 'Vendor Delivery Intake PO-' . rand(1000, 9999),
                'reference_no' => 'PO-' . rand(1000, 9999),
                'created_at' => now()->subDays(rand(6, 25)),
            ]);

            // Add an OUT movement if stock > 0 to simulate sales fulfillment
            if ($item['current_stock'] > 0) {
                StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'OUT',
                    'quantity' => 8,
                    'previous_stock' => $item['current_stock'] + 8,
                    'resulting_stock' => $item['current_stock'],
                    'reason' => 'Customer Invoice Order INV-' . rand(1000, 9999),
                    'reference_no' => 'INV-' . rand(1000, 9999),
                    'created_at' => now()->subDays(rand(1, 5)),
                ]);
            }
        }
    }
}
