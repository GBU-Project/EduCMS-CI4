<?php

namespace Tests\Unit;

use App\Libraries\UploadGuard;
use CodeIgniter\HTTP\Files\UploadedFile;
use CodeIgniter\Test\CIUnitTestCase;

class UploadGuardTest extends CIUnitTestCase
{
    public function testUploadGuardRejectsDangerousExtensions()
    {
        $guard = new UploadGuard();

        $fileMock = $this->createMock(UploadedFile::class);
        $fileMock->method('isValid')->willReturn(true);
        $fileMock->method('hasMoved')->willReturn(false);
        $fileMock->method('getClientName')->willReturn('shell.php');
        $fileMock->method('getClientExtension')->willReturn('php');

        $result = $guard->validateFile($fileMock, 'ppdb');
        $this->assertFalse($result['status']);
        $this->assertStringContainsString('Ekstensi file tidak diizinkan', $result['error']);
    }

    public function testUploadGuardRejectsDoubleExtensions()
    {
        $guard = new UploadGuard();

        $fileMock = $this->createMock(UploadedFile::class);
        $fileMock->method('isValid')->willReturn(true);
        $fileMock->method('hasMoved')->willReturn(false);
        $fileMock->method('getClientName')->willReturn('image.jpg.php');
        $fileMock->method('getClientExtension')->willReturn('jpg');

        $result = $guard->validateFile($fileMock, 'ppdb');
        $this->assertFalse($result['status']);
        $this->assertStringContainsString('mengandung ekstensi berbahaya', $result['error']);
    }
}
