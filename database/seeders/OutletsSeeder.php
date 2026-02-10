<?php

namespace Database\Seeders;

use App\Models\Outlet;
use App\Models\OutletSocialLink;
use Illuminate\Database\Seeder;

class OutletsSeeder extends Seeder
{
    public function run(): void
    {
        $outlets = [
            [
                'name' => 'Grand Hotel Bahrain',
                'code' => 'grand-hotel',
                'description' => '5-star luxury hotel in the heart of Manama',
                'type' => 'hotel',
                'city' => 'Manama',
                'address' => 'Diplomat Area, P.O. Box 10505',
                'country' => 'BH',
                'phone' => '+973-17581111',
                'email' => 'info@grandhotel.bh',
                'timezone' => 'Asia/Bahrain',
                'currency' => 'BHD',
                'active' => true,
            ],
            [
                'name' => 'Seaside Resort & Spa',
                'code' => 'seaside-resort',
                'description' => 'Beachfront resort with private beach and spa',
                'type' => 'resort',
                'city' => 'Muharraq',
                'address' => 'Coastal Road, Muharraq',
                'country' => 'BH',
                'phone' => '+973-17563333',
                'email' => 'reservations@seaside.bh',
                'timezone' => 'Asia/Bahrain',
                'currency' => 'BHD',
                'active' => true,
            ],
            [
                'name' => 'The Irish Pub',
                'code' => 'irish-pub',
                'description' => 'Authentic Irish pub with live music and great drinks',
                'type' => 'bar',
                'city' => 'Manama',
                'address' => 'Juffair Avenue, Manama',
                'country' => 'BH',
                'phone' => '+973-17299999',
                'email' => 'info@irishpub.bh',
                'timezone' => 'Asia/Bahrain',
                'currency' => 'BHD',
                'active' => true,
            ],
            [
                'name' => 'La Piazza Restaurant',
                'code' => 'la-piazza',
                'description' => 'Italian fine dining restaurant',
                'type' => 'restaurant',
                'city' => 'Manama',
                'address' => 'Block 338, Adliya',
                'country' => 'BH',
                'phone' => '+973-17700000',
                'email' => 'reservations@lapiazza.bh',
                'timezone' => 'Asia/Bahrain',
                'currency' => 'BHD',
                'active' => true,
            ],
            [
                'name' => 'Skyline Nightclub',
                'code' => 'skyline-club',
                'description' => 'Premium nightclub with VIP areas and DJ nights',
                'type' => 'club',
                'city' => 'Manama',
                'address' => 'Kuwait Building, Manama',
                'country' => 'BH',
                'phone' => '+973-17298888',
                'email' => 'events@skyline.bh',
                'timezone' => 'Asia/Bahrain',
                'currency' => 'BHD',
                'active' => true,
            ],
        ];

        foreach ($outlets as $outletData) {
            $outlet = Outlet::firstOrCreate(
                ['code' => $outletData['code']],
                $outletData
            );

            // Create social links for each outlet
            $this->createSocialLinks($outlet);
        }
    }

    protected function createSocialLinks(Outlet $outlet): void
    {
        $socialLinks = [
            ['platform' => 'instagram', 'label' => 'Follow us', 'url' => 'https://instagram.com/' . $outlet->code, 'sort_order' => 1],
            ['platform' => 'facebook', 'label' => 'Like us', 'url' => 'https://facebook.com/' . $outlet->code, 'sort_order' => 2],
            ['platform' => 'whatsapp', 'label' => 'Chat with us', 'url' => 'https://wa.me/' . str_replace(['+', '-'], '', $outlet->phone), 'sort_order' => 3],
            ['platform' => 'website', 'label' => 'Visit Website', 'url' => 'https://' . $outlet->code . '.bh', 'sort_order' => 4],
        ];

        foreach ($socialLinks as $link) {
            OutletSocialLink::firstOrCreate(
                ['outlet_id' => $outlet->id, 'platform' => $link['platform']],
                array_merge($link, ['is_active' => true])
            );
        }
    }
}
