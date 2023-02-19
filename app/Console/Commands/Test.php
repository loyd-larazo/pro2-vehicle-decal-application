<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Test extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'test';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Command description';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle()
  {
    dd(QrCode::size(300)->generate('https://techvblogs.com/blog/generate-qr-code-laravel-8'));
  }
}
