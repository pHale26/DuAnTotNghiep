<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Drop the unique index on identity_number if it exists
        // The index is named 'identity_number' in DB
        try {
            Schema::table('user_identities', function (Blueprint $table) {
                $table->dropUnique('identity_number');
            });
        } catch (Exception $e) {
            // Ignore if index doesn't exist
        }

        // 2. Add identity_number_hash column and change identity_number to text
        Schema::table('user_identities', function (Blueprint $table) {
            $table->text('identity_number')->change();
            if (! Schema::hasColumn('user_identities', 'identity_number_hash')) {
                $table->string('identity_number_hash', 64)->nullable()->after('identity_number');
            }
        });

        // 3. Encrypt existing values and generate hashes
        $records = DB::table('user_identities')->get();
        foreach ($records as $record) {
            $rawNumber = $record->identity_number;

            // Check if already encrypted (in case migration is rerun or data is corrupted)
            $isEncrypted = false;
            try {
                Crypt::decryptString($rawNumber);
                $isEncrypted = true;
            } catch (Exception $e) {
                $isEncrypted = false;
            }

            $encryptedValue = $isEncrypted ? $rawNumber : Crypt::encryptString($rawNumber);
            $plainText = $isEncrypted ? Crypt::decryptString($rawNumber) : $rawNumber;
            $hashValue = hash('sha256', $plainText);

            DB::table('user_identities')
                ->where('id', $record->id)
                ->update([
                    'identity_number' => $encryptedValue,
                    'identity_number_hash' => $hashValue,
                ]);
        }

        // 4. Make identity_number_hash not nullable and unique
        Schema::table('user_identities', function (Blueprint $table) {
            $table->string('identity_number_hash', 64)->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Decrypt identity_number back to plaintext (capped at 20 chars) and drop unique on hash
        $records = DB::table('user_identities')->get();
        foreach ($records as $record) {
            try {
                $decrypted = Crypt::decryptString($record->identity_number);
                DB::table('user_identities')
                    ->where('id', $record->id)
                    ->update([
                        'identity_number' => substr($decrypted, 0, 20),
                    ]);
            } catch (Exception $e) {
                // Keep as is if decryption fails
            }
        }

        Schema::table('user_identities', function (Blueprint $table) {
            $table->dropUnique('user_identities_identity_number_hash_unique');
            $table->dropColumn('identity_number_hash');
        });

        // 2. Change identity_number back to varchar(20) and add unique constraint back
        Schema::table('user_identities', function (Blueprint $table) {
            $table->string('identity_number', 20)->change();
            $table->unique('identity_number', 'identity_number');
        });
    }
};
