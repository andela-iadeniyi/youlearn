<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class IndexTest extends TestCase
{
    use YouLearn\Test\CreateTrait;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /**
     * Test Home Page LoadsCorrectly
     *
     * @return void
     */
    public function testHomePageLoadsCorrectly()
    {
        $this->call('GET', '/');

        $this->assertResponseOk();
    }

    /**
     * Test view has categories
     *
     * @return void
     */
    public function testVIewHasCategory()
    {
        $this->visit('/');

        $this->assertViewHas('categories');
    }

    /**
     * Test visit home page
     *
     * @return void
     */
    public function testHomePage()
    {
        $this->visit('/')
             ->see('Welcome to YouLearn');
    }

    /**
     * Test Load Registration Page
     *
     * @return void
     */
    public function testSeeRegistrationPage()
    {
        $this->visit('/')
             ->click('Register')
             ->see('Registration Form');
    }

    /**
     * Test Load Load Page
     *
     * @return void
     */
    public function testSeeLoginPage()
    {
        $this->visit('/')
             ->click('Login')
             ->see('Please sign in');
    }

    /**
     * Test load dashboard
     *
     * @return void
     */
    public function testLoadDashboard()
    {
        $this->visit('/login')
             ->see('Please sign in')
             ->type('testuser', 'username')
             ->type('testpassword', 'password')
             ->press('Sign In');

        $this->call('GET', '/');

        $this->assertResponseOk();
    }

    /**
     * Test logut route
     *
     * @return void
     */
    public function testLogout()
    {
        $this->login();

        $this->visit('/')
             ->see('Logout')
             ->click('Logout');

        $this->assertResponseOk();
    }
}
