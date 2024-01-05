<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Instagram\Api;

class PostInstagramReels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'post:insta-reels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client();
//        $response = $client->get("https://www.instagram.com/p/Ctg08REgFI0/?__a=1&__d=dis");
        $cachePool = new FilesystemAdapter('Instagram', 0, __DIR__ . '/../cache');
        $api = new Api($cachePool);
        $api->login('babybara', 'BloomingToe!!');
        $reelsFeed = [];

//        $reelsFeed = $api->getReels(25288085);
//        dd($reelsFeed->getReels());
        $userNames = ['hiropyon1116', 'serious.bara', 'minokapi', 'capisabara', 'bapybara',
          'capybara.worlds', 'ilove.capybara', 'capybarasenthusiasts'];

        foreach($userNames as $userName) {
            $profile = $api->getProfile($userName);
            if($profile)
                $reelsFeed[] = $api->getReels($profile->getId());
        }
//        $reelsFeed = $api->getReels($profile->getId());

//        dd(count($reelsFeed));

    }
//        dd($reelsFeed);

//        dd($instagram->getDownloadLink());


}
