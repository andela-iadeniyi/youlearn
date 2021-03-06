<?php

use YouLearn\User;
use YouLearn\Video;
use YouLearn\Avatar;
use YouLearn\Category;
use YouLearn\Http\Controllers\VideoController;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class VideoTest extends TestCase
{
    use YouLearn\Test\CreateTrait;

    protected $video;

    public function __construct() {
        $this->video = new VideoController();
    }

    /**
     * Test video upload
     *
     * @return void
     */
    public function testUploadSuccessful()
    {
        $this->login();

        $this->visit('/video/upload')
             ->see('Video Upload')
             ->type(1, 'user_id')
             ->select(1, 'category_id')
             ->type('Test Title', 'title')
             ->type('youtube_video', 'url')
             ->press('Submit')
             ->seeInDatabase('videos', ['title' => 'Test Title']);
    }

    /**
     * Test cannot create video with empty field
     *
     * @return void
     */
    public function testEmptyFieldVideoUploadError()
    {
        $this->login();

        $this->visit('/video/upload')
             ->see('Video Upload')
             ->type(1, 'user_id')
             ->select('', 'category_id')
             ->type('', 'title')
             ->type('youtube_video', 'url')
             ->press('Submit')
             ->see('{"message":"Input field is empty","status_code":400}');
    }

    /**
     * Test cannot create video with empty field
     *
     * @return void
     */
    public function testInvalidYoutubeVideoUploadError()
    {
        $this->login();

        $this->visit('/video/upload')
             ->see('Video Upload')
             ->type(1, 'user_id')
             ->select('1', 'category_id')
             ->type('New Title', 'title')
             ->type('htt://pyahoo.com', 'url')
             ->press('Submit')
             ->see('{"message":"Invalid Youtube URL","status_code":400}');
    }

    /**
     * Test cannot create video
     *
     * @return void
     */
    public function testVideoUploadError()
    {
        $this->login();

        $this->visit('/video/upload')
             ->see('Video Upload')
             ->type(1, 'user_id')
             ->type('', 'category_id')
             ->type('', 'title')
             ->type('https://www.youtube.com/watch?v=7TF00hJI78Y', 'url')
             ->press('Submit')
             ->see('{"message":"Input field is empty","status_code":400}');
    }

    /**
     * Test video is not youtube
     */
    public function testVideoIsNotYoutube()
    {
        $videoID = 'https://www.yahoo.com/watch?v=7TF00hJI78Y';

        $this->assertFalse($this->video->youtubeExist($videoID));
    }

    /**
     * Test video is youtube
     */
    public function testVideoIsYoutube()
    {
        $videoID = 'https://www.youtube.com/watch?v=7TF00hJI78Y';

        $this->assertTrue($this->video->youtubeExist($videoID));
    }

    /**
     * Test User cannot edit video
     *
     * @return void
     */
    public function testCannotEditVideo()
    {
        $this->put('/video/Test-title/edit', ['title' => 'New Title'], ['text/html']);

        $this->assertResponseStatus(500);
    }

    /**
     * test Play Video
     *
     * @return void
     */
    public function testPlayVideoPage()
    {
        $this->login();

        $this->visit('/')
             ->see('VIEW')
             ->click('view')
             ->see('Created on');
    }

    /**
     *
     */
    public function testUserVideo()
    {
        $this->login();

        $this->visit('/user/videos')
             ->see('EDIT');

        $this->assertViewHas('videos');
    }

    /**
     * Test video edit
     *
     * @return void
     */
    public function testEditVideo()
    {
        $this->login();

        $this->visit('/user/videos')
             ->see('Video Title')
             ->click('edit')
             ->type('Latest Title', 'title')
             ->press('Update')
             ->seeInDatabase('videos', ['title' => 'Latest Title']);
    }

    /**
     * Test video edit
     *
     * @return void
     */
    public function testEditVideoError()
    {
        $this->login();

        $this->visit('/user/videos')
             ->click('edit')
             ->type('', 'title')
             ->press('Update')
             ->see('{"message":"Input field is empty","status_code":400}');
    }

    /**
     * Test play video
     *
     * @return void
     */
    public function testPlayVideo()
    {
        $this->login();

        $this->visit('/')
             ->click('view')
             ->see('Uploaded by');

        $this->assertViewHas('video');
    }

    /**
     * Test delete video
     *
     * @return void
     */
    public function testDeleteVideo()
    {
        $this->login();

        $this->visit('/user/videos')
             ->click('edit')
             ->click('deleteVideo')
             ->see('Video deleted successfully');
    }

    /**
     * Test get video ID
     */
    public function testGetVideoId()
    {
        $videoID = 'https://www.youtube.com/watch?v=7TF00hJI78Y';

        $this->assertInternalType('string', $this->video->getVideoId($videoID));
    }

    public function testVideoCategoryRelationship()
    {
        $this->createUser();
        $this->createCategory();
        $video = $this->createVideo();

        $this->assertEquals($video->category_id, $video->category->id);
    }

    public function testVideoUserRelationship()
    {
        $this->createUser();
        $this->createCategory();
        $video = $this->createVideo();

        $this->assertEquals($video->user_id, $video->user->id);
    }
}
