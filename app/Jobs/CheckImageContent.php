<?php

namespace App\Jobs;

use App\Events\PostInteraction;
use App\Models\Post;
use App\Notifications\PostViolationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Sightengine\SightengineClient;

class CheckImageContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;


    public function __construct($post)
    {
        $this->post = $post;


    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = new SightengineClient(env('SIGHTENGINE_USER'), env('SIGHTENGINE_SECRET'));
        $output = $client->check(['nudity-2.0',
//                'offensive',
//                'tobacco',
//                'wad',
        ])->set_file(storage_path('app/public/posts/' . $this->post->image));

        if ($output->nudity->sexual_activity >= 0.5 ||
            $output->nudity->sexual_display >= 0.5 ||
            $output->nudity->erotica >= 0.5||

//                $output->offensive->prob >= 0.5 ||
            $output->gore->prob >= 0.5
//                $output->gambling->prob >= 0.5 ||
//                $output->tobacco->prob >= 0.5 ||
//                $output->weapon>= 0.5 ||
//                $output->alcohol>= 0.5 ||
//                $output->drugs>= 0.5
        ){
            $this->post->user->notify(new PostViolationNotification($this->post));
            broadcast(new PostInteraction($this->post, 'violation'))->toOthers();
            Storage::delete('public/posts/' . $this->post->image);
            $this->post->delete();
        }

    }
}
