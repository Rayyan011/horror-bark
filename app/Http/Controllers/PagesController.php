<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Facades\View;
use App\Models\Page;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function show($page_name)
    {
        // Retrieve the page by its name
        $page = Page::where('page_name', $page_name)->first();
        if (!$page) {
            // Print a message if no page is found
            return response("No page found with the name: {$page_name}", 404);
        }

        // Build the page data from the page content
        $page_data = [];
        if (isset($page->content)) {
            foreach ($page->content as $value) {
                $page_data[$value['data']['pos']] = [
                    'content' => $value['data']['content'],
                    'type'    => $value['type'],
                ];
            }
        }

        // Determine the view template to use
        $view_template = 'pages.' . $page_name; // Adjust the view path as needed

        try {
            if (View::exists($view_template)) {
                return view($view_template, [
                    $page_name   => $page_data,
                    'page_name' => $page_name,
                ]);
            } else {
                throw new Exception("View does not exist.");
            }
        } catch (Exception $e) {
            report($e);
            // Print a message if the view template is not found
            return response("View for page '{$page_name}' not found.", 404);
        }
    }
}