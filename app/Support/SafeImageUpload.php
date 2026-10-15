<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

/**
 * Upload gambar JPG/PNG yang tahan tamper.
 *
 * Ekstensi & Content-Type dari browser bisa dipalsukan, jadi yang dipercaya hanya isi file
 * (magic bytes). File disimpan hasil re-encode dengan nama acak + ekstensi dari server,
 * sehingga payload yang disisipkan (polyglot, komentar EXIF, data setelah EOF) ikut terbuang.
 */
class SafeImageUpload
{
    public const EXTENSIONS = ['jpg', 'jpeg', 'png'];
    public const MAX_KB = 2048;

    private const MIME_TO_EXT = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
    ];

    private const FORBIDDEN_SEGMENT = '/^(php\d*|phtml|phar|pht|phps|pgif|inc|shtml|htaccess|htpasswd|user\.ini|cgi|pl|py|rb|asp|aspx|ashx|jsp|jspx|sh|bat|cmd|exe|dll|svg|svgz|html?|xhtml|js|mjs)$/';

    private const FORBIDDEN_CONTENT = '/<\?php|<script|__halt_compiler/i';

    /**
     * @return string|null pesan error, atau null kalau file aman.
     */
    public static function check(UploadedFile $file, int $maxKb = self::MAX_KB): ?string
    {
        if (!$file->isValid()) {
            return 'File gagal diupload, silakan coba lagi.';
        }

        $name = (string) $file->getClientOriginalName();
        if ($name === '' || preg_match('/[\x00-\x1f\x7f\/\\\\:]/', $name)) {
            return 'Nama file tidak valid.';
        }

        $segments = explode('.', strtolower(trim($name)));
        $extension = array_pop($segments);
        if (!$segments || !in_array($extension, self::EXTENSIONS, true)) {
            return 'Format file harus JPG, JPEG, atau PNG.';
        }

        foreach ($segments as $segment) {
            if (preg_match(self::FORBIDDEN_SEGMENT, trim($segment))) {
                return 'Nama file mengandung ekstensi yang tidak diizinkan.';
            }
        }

        if ((int) $file->getSize() < 1 || (int) $file->getSize() > $maxKb * 1024) {
            return 'Ukuran file maksimal ' . rtrim(rtrim(number_format($maxKb / 1024, 1), '0'), '.') . 'MB.';
        }

        $path = $file->getRealPath();
        if (!$path || !is_file($path)) {
            return 'File gagal diupload, silakan coba lagi.';
        }

        $mime = self::detectMime($path);
        if (!isset(self::MIME_TO_EXT[$mime])) {
            return 'Isi file bukan gambar JPG/PNG.';
        }

        $info = @getimagesize($path);
        if (
            !$info
            || !in_array($info[2] ?? null, [IMAGETYPE_JPEG, IMAGETYPE_PNG], true)
            || ($info[0] ?? 0) < 1
            || ($info[1] ?? 0) < 1
        ) {
            return 'File gambar rusak atau tidak valid.';
        }

        $content = @file_get_contents($path);
        if ($content === false || preg_match(self::FORBIDDEN_CONTENT, $content)) {
            return 'File mengandung kode yang tidak diizinkan.';
        }

        return null;
    }

    /**
     * Simpan hasil re-encode. Panggil hanya setelah check() lolos.
     */
    public static function store(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $source = $file->getRealPath();
        $extension = self::MIME_TO_EXT[self::detectMime($source)] ?? 'jpg';

        $image = (new ImageManager(new Driver()))->read($source);
        $encoded = $extension === 'png' ? $image->toPng() : $image->toJpeg(90);

        $path = trim($directory, '/') . '/' . Str::random(40) . '.' . $extension;
        Storage::disk($disk)->put($path, $encoded->toString());

        return $path;
    }

    private static function detectMime(string $path): string
    {
        return (string) (new \finfo(FILEINFO_MIME_TYPE))->file($path);
    }
}
