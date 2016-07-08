<?php

use App\Libraries\Path\Path;
use App\Libraries\Twitter\Service;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/');

        $this->assertEquals(
            $this->response->getContent(), $this->app->version()
        );
    }

    public function testPathGetImageFromMoment()
    {
        $path = new Path();
        $moment_url = 'https://path.com/moment/1agC2s';
       // dd($path::getImageFromMoment($moment_url));
    }

    public function testSearchFromPathDaily()
    {
        $twitter = new Service();
        var_dump($twitter->search('#pathdaily'));
    }


    public function testSearch()
    {
        $twitter = new Service();
        var_dump($twitter->search('#image'));
    }
}
