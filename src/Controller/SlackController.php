<?php

namespace App\Controller;

use App\Entity\Idea;
use App\Service\SlackService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_USER')]
class SlackController extends AbstractController
{
    #[Route('/slack', name: 'app_slack')]
    public function index(): Response
    {
        return $this->render('slack/index.html.twig', [
            'controller_name' => 'SlackController',
        ]);
    }

    #[Route('/createchannel', name: 'create_channel')]
    public function createChannel(SlackService $slackService, Idea $ideaTitle, SluggerInterface $slugger): Response
    {
        $channelName = $ideaTitle->getTitle(); // Set the channel name based on idea name

        $slug = $slugger->slug($channelName, '_'); // Apply the slugger to the channel name

        $channel = $slackService->createChannel($slug);
        // Call the createChannel method of SlackService with the specified channel name with slug

        if ($channel['ok']) {
            $channelId = $channel['channel']['id']; // Extract the channel ID from the response
            $message = "New channel created: {$channelName} (ID: {$channelId}).";
            // Create a success message with the channel name and ID
        } else {
            $error = $channel['error']; // Extract the error message from the response
            $message = "Failed to create channel: {$error}."; // Create an error message with the error details
        }

        return $this->render('slack/create_channel.html.twig', [
            'message' => $message, // Pass the message to the 'slack/create_channel.html.twig' template
        ]);
    }
}
