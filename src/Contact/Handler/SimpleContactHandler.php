<?php

declare(strict_types=1);

namespace Minimal\Contact\Handler;

use Laminas\Diactoros\Response\HtmlResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Simple contact handler for debugging.
 */
class SimpleContactHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <title>Contact Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #1a1a2e; color: white; }
        .container { max-width: 800px; margin: 0 auto; }
        .form { background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white; border-radius: 5px; }
        button { background: linear-gradient(45deg, #667eea 0%, #764ba2 100%); color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Contact Us - Simple Test</h1>
        <div class="form">
            <form method="post" action="/contact">
                <div>
                    <label>Name:</label>
                    <input type="text" name="first-name" required>
                </div>
                <div>
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div>
                    <label>Message:</label>
                    <textarea name="message" rows="5" required></textarea>
                </div>
                <button type="submit">Send Message</button>
            </form>
        </div>
    </div>
</body>
</html>';

        return new HtmlResponse($html);
    }
}
