<?php

class TbConfigurationTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('tb_configuration')->truncate();
        
		\DB::table('tb_configuration')->insert(array (
			0 => 
			array (
				'id' => '1',
				'name' => 'withdrawal_email',
				'values' => '{"help_email":"help@topbetta.com","sender_email":"help@topbetta.com","sender_name":"TopBetta Account","withdrawal_notify_email_subject":"TopBetta - Withdrawal Request","withdrawal_notify_email_body":"Hello [first name],\\r\\n\\r\\nA request for withdrawal was recently made for your TopBetta account. Here are the details:\\r\\n[requested date]\\r\\n[amount]\\r\\nto [withdrawal method] [withdrawal account]\\r\\n\\r\\nIf this is incorrect, please notify us immediately at [help email]. Otherwise we will notify you as soon as the withdrawal has been processed.\\r\\n\\r\\nRegards,\\r\\n\\r\\nThe TopBetta Team\\r\\n[help email]\\r\\n","withdrawal_approval_email_subject":"TopBetta - Withdrawal Request Approved","withdrawal_approval_email_body":"Hello [first name],\\r\\n\\r\\nThe following TopBetta account withdrawal request has now been processed.\\r\\n\\r\\n[requested date]\\r\\n[amount]\\r\\nto [withdrawal method] [withdrawal account]\\r\\n\\r\\nRegards,\\r\\n\\r\\nThe TopBetta Team\\r\\n[help email]\\r\\n","withdrawal_denial_email_subject":"TopBetta - Withdrawal Request Denied","withdrawal_denial_email_body":"Hello [first name],\\r\\n\\r\\nThe following TopBetta account withdrawal request has been rejected. Here are the details:\\r\\n\\r\\n[requested date]\\r\\n[amount]\\r\\nto [withdrawal method] [withdrawal account]\\r\\nReason: [notes]\\r\\n\\r\\nIf you feel this is in error, please contact us at [help email]. \\r\\n\\r\\nRegards,\\r\\n\\r\\nThe TopBetta Team\\r\\n[help email]"}',
				'created_at' => '2015-04-15 13:41:01',
				'updated_at' => '2015-04-15 15:34:20',
			),
			1 => 
			array (
				'id' => '2',
				'name' => 'withdrawal_email_variables',
			'values' => '{	"first name" : { "description" : "customer\'s first name e.g. \\"John\\"", "value" : "user.topbettauser.first_name" }, 	"name" : {"description" : "customer\'s full name, e.g. \\"John Mayer\\"", "value" : "user.name"},	"requested date" : { "description" : "the date of the request e.g. \\"August 30, 2010, 2:41 pm\\"", "value" : "requested_date"},	"amount" : {"description": "the amount of withdrawal, e.g. \\"$100.00\\"", "value" : "amount" },	"amount raw" : {"description" : "the raw number of withdrawal amount, e.g. \\"100\\"", "value" : "amount" }, 	"withdrawal method" : {"description" : "how customer wants to get the withdrawal, e.g. \\"PayPalyAccount\\" or \\"Bank Account\\"", "value" : "type.name"},	"withdrawal account" : {"description" : "the withdrawal account (only for paypal), e.g. \\"paypal-email@example.com\\"", "value" : "paypal.paypal_id"},	"help email" : {"description" : "the help email which is set up in this page, e.g. \\"help@topbetta.com\\""},	"notes" : {"description" : "the notes which were entered when the request was approved/denied", "value" : "notes"}}',
				'created_at' => '2015-04-15 16:03:26',
				'updated_at' => '2015-04-15 16:03:27',
			),
		));
	}

}
