<?php
use Migrations\AbstractMigration;

class POCOR7147 extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     * @return void
     */
    public function up()
    {
        // Backup locale_contents table
        $this->execute('CREATE TABLE `z_7147_locale_contents` LIKE `locale_contents`');
        $this->execute('INSERT INTO `z_7147_locale_contents` SELECT * FROM `locale_contents`');
        // End
		
		$localeContent = [

            [
                'en' => 'Types of disability',
                'created_user_id' => 1,
                'created' => date('Y-m-d H:i:s')
            ],
            [
                'en' => 'Disability degree',
                'created_user_id' => 1,
                'created' => date('Y-m-d H:i:s')
            ]
            
        ];
        $this->insert('locale_contents', $localeContent);
    }

    // rollback
    public function down()
    {
        $this->execute('DROP TABLE IF EXISTS `locale_contents`');
        $this->execute('RENAME TABLE `z_7147_locale_contents` TO `locale_contents`');
    }
}
