<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $post;
    protected $filePath;

    public function __construct($post,$filePath)
    {
        $this->post = $post;
        $this->filePath = $filePath;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $fileName = $this->post->id . '.' . now()->format('YmdHis') . '.webp';
        // Use Intervention Image here
        $manager = new ImageManager(new Driver());

        $image = $manager->read(Storage::path($this->filePath)); // Read the file from the temporary location
        $image->toWebp(80); // Convert the image to webp format (80% quality)
        $image->save(Storage::path('public/posts/' . $fileName)); // Save the image to the public disk

        // Delete the temporary file
        Storage::delete($this->filePath);

        $this->post->image = $fileName;
        $this->post->save();

        //dispatch a job to check image content
        CheckImageContent::dispatch($this->post);
    }

}
