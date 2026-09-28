<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptIdentityImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'identity:encrypt-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mã hóa (encrypt) các URL ảnh CCCD đang lưu dạng plaintext trong bảng user_identities';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $rows = DB::table('user_identities')
            ->whereNotNull('front_image_url')
            ->orWhereNotNull('back_image_url')
            ->get(['id', 'front_image_url', 'back_image_url']);

        if ($rows->isEmpty()) {
            $this->info('Không có bản ghi nào cần mã hóa.');

            return self::SUCCESS;
        }

        $this->info("Tìm thấy {$rows->count()} bản ghi cần xử lý...");
        $bar = $this->output->createProgressBar($rows->count());
        $bar->start();

        $encrypted = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $updates = [];

            if (! empty($row->front_image_url) && ! $this->isAlreadyEncrypted($row->front_image_url)) {
                $updates['front_image_url'] = Crypt::encryptString($row->front_image_url);
            }

            if (! empty($row->back_image_url) && ! $this->isAlreadyEncrypted($row->back_image_url)) {
                $updates['back_image_url'] = Crypt::encryptString($row->back_image_url);
            }

            if (! empty($updates)) {
                DB::table('user_identities')->where('id', $row->id)->update($updates);
                $encrypted++;
            } else {
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ Hoàn thành! Đã mã hóa: {$encrypted} bản ghi | Bỏ qua (đã mã hóa): {$skipped} bản ghi.");

        return self::SUCCESS;
    }

    /**
     * Kiểm tra xem chuỗi đã được encrypt chưa (tránh mã hóa 2 lần).
     * Crypt::encryptString trả về chuỗi JSON được base64 encode.
     */
    private function isAlreadyEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);

            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
