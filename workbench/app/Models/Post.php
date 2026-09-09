<?php

declare(strict_types=1);

namespace Workbench\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Workbench\Database\Factories\PostFactory;

/**
 * The Workbench's stand-in for a piece of application content.
 *
 * A consuming application would own this model; the package only
 * supplies the fake content its factory stores in the body columns.
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'title',
        'slug',
        'markdown_content',
        'html_content',
        'rich_content',
    ];

    #[\Override]
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
