<?php

use Phinx\Seed\AbstractSeed;

class Init extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $user_data = array();
        for($i = 1; $i <= 10; $i++) {
            $user_data[] = array(
                'username' => 'test'.$i,
                'password' => $this->hash('aaa'.$i,'abcdef'.$i),
                'salt'     => 'aaa'.$i,
                'mail'     => 'test'.$i.'@test.com',
                'integral' => 1000.1,
                'recommend'=> $i,
            );
        }
        $user = $this->table('user');
        $user->insert($user_data)->save();

        $shop_data = array();
        $shop_data[] = array('name'=> 'ticket', 'desc'=>'下一关的门票:)', 'amount'=>100, 'price'=> 1000000.1);
        for($i = 1; $i <= 100; $i++) {
            $shop_data[] = array(
                'name'=> $this->get_random(5),
                'desc'=> $this->get_random(50),
                'amount'=> rand(10, 200),
                'price'=> rand(1000, 8000) / 10,
            );
        }
        $shop_data[] = array('name'=> 'goodthing', 'desc'=>'for_check', 'amount'=> 10000, 'price'=> 1.13);
        $commoditys = $this->table('commoditys');
        $commoditys->insert($shop_data)->save();

        $config_data = array();
        $config_data[] = array(
            'pay_key'=> $this->get_random(15),
        );
        $config = $this->table('pay_config');
        $config->insert($config_data)->save();
    }

    private function get_random($length) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $password;
    }

    private function hash($salt, $password) {
        return md5(sha1($salt.$password));
    }
}
