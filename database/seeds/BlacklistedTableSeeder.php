<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlacklistedTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('blacklisted')->insert([
			[    
			    
				'blacklisted_host' => 'dnsbl-1.uceprotect.net',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'blacklisted_host' => 'dnsbl-2.uceprotect.net',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'blacklisted_host' => 'dnsbl-3.uceprotect.net',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'blacklisted_host' => 'dnsbl.dronebl.org',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'blacklisted_host' => 'dnsbl.sorbs.net',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'blacklisted_host' => 'zen.spamhaus.org',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'blacklisted_host' => 'bl.spamcop.net',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'blacklisted_host' => 'list.dsbl.org',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'blacklisted_host' => 'sbl.spamhaus.org',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'blacklisted_host' => 'xbl.spamhaus.org',
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]

	   ]);
    }
}

