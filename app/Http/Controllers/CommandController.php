<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class CommandController extends Controller
{
    /**
     * Execute a terminal command and return the output
     *
     * @param Request $request
     * @param string $command The command to execute
     * @return \Illuminate\Http\Response
     */
    public function executeCommand(Request $request, $command = null)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. You must be logged in to execute commands.',
            ], 401);
        }

        // If no command is provided in the URL, check if it's in the request
        if (!$command && $request->has('commanhttps://intellicrafts.in/d')) {
            $command = $request->input('command');
        }

        // If still no command, return an error
        if (!$command) {
            return response()->json([
                'status' => 'error',
                'message' => 'No command provided.',
            ], 400);
        }

        // Decode URL-encoded command
        $command = urldecode($command);

        // Whitelist of allowed commands
        $allowedCommands = [
            'php artisan',
            'git',
            'composer',
            'npm',
            'ls',
            'cat',
            'echo',
            'tail',
            'grep'
        ];

        // Check if the command starts with any of the allowed commands
        $isAllowed = false;
        foreach ($allowedCommands as $allowedCommand) {
            if (strpos($command, $allowedCommand) === 0) {
                $isAllowed = true;
                break;
            }
        }

        // If command is not allowed, return an error
        if (!$isAllowed) {
            return response()->json([
                'status' => 'error',
                'message' => 'Command not allowed for security reasons.',
                'allowed_commands' => $allowedCommands
            ], 403);
        }

        try {
            // Execute the command
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(60);
            $process->run();
            
            // Get the output and error output
            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();
            $exitCode = $process->getExitCode();

            // Prepare the response
            $response = [
                'status' => $exitCode === 0 ? 'success' : 'error',
                'exit_code' => $exitCode,
                'command' => $command,
                'output' => $output,
            ];

            // Add error output if there is any
            if (!empty($errorOutput)) {
                $response['error_output'] = $errorOutput;
            }

            return response()->json($response);
        } catch (ProcessFailedException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to execute command.',
                'command' => $command,
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    /**
     * Render a view to display command execution results
     *
     * @param Request $request
     * @param string $command The command to execute
     * @return \Illuminate\Http\Response
     */
    public function executeCommandView(Request $request, $command = null)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return view('command.error', [
                'error' => 'Unauthorized. You must be logged in to execute commands.'
            ]);
        }

        // If no command is provided in the URL, check if it's in the request
        if (!$command && $request->has('command')) {
            $command = $request->input('command');
        }

        // If still no command, return an error
        if (!$command) {
            return view('command.error', [
                'error' => 'No command provided.'
            ]);
        }

        // Decode URL-encoded command
        $command = urldecode($command);

        // Whitelist of allowed commands
        $allowedCommands = [
            'php artisan',
            'git',
            'composer',
            'npm',
            'ls',
            'cat',
            'echo',
            'tail',
            'grep'
        ];

        // Check if the command starts with any of the allowed commands
        $isAllowed = false;
        foreach ($allowedCommands as $allowedCommand) {
            if (strpos($command, $allowedCommand) === 0) {
                $isAllowed = true;
                break;
            }
        }

        // If command is not allowed, return an error
        if (!$isAllowed) {
            return view('command.error', [
                'error' => 'Command not allowed for security reasons.',
                'allowed_commands' => $allowedCommands
            ]);
        }

        try {
            // Execute the command
            $process = Process::fromShellCommandline($command);
            $process->setTimeout(60);
            $process->run();
            
            // Get the output and error output
            $output = $process->getOutput();
            $errorOutput = $process->getErrorOutput();
            $exitCode = $process->getExitCode();

            // Prepare the data for the view
            $data = [
                'status' => $exitCode === 0 ? 'success' : 'error',
                'exit_code' => $exitCode,
                'command' => $command,
                'output' => $output,
                'error_output' => $errorOutput,
            ];

            return view('command.result', $data);
        } catch (ProcessFailedException $exception) {
            return view('command.error', [
                'error' => 'Failed to execute command: ' . $exception->getMessage(),
                'command' => $command
            ]);
        }
    }
}