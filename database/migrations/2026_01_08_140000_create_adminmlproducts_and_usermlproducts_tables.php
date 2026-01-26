<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('mlproducts') && ! Schema::hasTable('adminmlproducts')) {
            Schema::rename('mlproducts', 'adminmlproducts');
        }

        if (! Schema::hasTable('adminmlproducts')) {
            Schema::create('adminmlproducts', function (Blueprint $table) {
                $table->id();
                $table->string('product_id');
                $table->string('name')->nullable();
                $table->string('diamonds')->default('0');
                $table->integer('price')->default(0);
                $table->string('category')->nullable();
                $table->string('region')->default('myanmar')->index();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
                $table->unique(['product_id', 'region']);
            });
        } else {
            Schema::table('adminmlproducts', function (Blueprint $table) {
                if (! Schema::hasColumn('adminmlproducts', 'region')) {
                    $table->string('region')->default('myanmar')->index();
                }
                if (! Schema::hasColumn('adminmlproducts', 'status')) {
                    $table->boolean('status')->default(true)->index();
                }
                if (! Schema::hasColumn('adminmlproducts', 'diamonds')) {
                    $table->string('diamonds')->default('0');
                }
                if (! Schema::hasColumn('adminmlproducts', 'price')) {
                    $table->integer('price')->default(0);
                }
            });
        }

        if (! Schema::hasTable('usermlproducts')) {
            Schema::create('usermlproducts', function (Blueprint $table) {
                $table->id();
                $table->string('product_id');
                $table->string('name')->nullable();
                $table->string('diamonds')->default('0');
                $table->integer('price')->default(0);
                $table->string('category')->nullable();
                $table->string('region')->default('myanmar')->index();
                $table->boolean('status')->default(true)->index();
                $table->timestamps();
                $table->unique(['product_id', 'region']);
            });
        }

        if (Schema::hasTable('adminmlproducts') && Schema::hasTable('usermlproducts')) {
            $userCount = DB::table('usermlproducts')->count();
            if ($userCount === 0) {
                DB::table('adminmlproducts')
                    ->orderBy('id')
                    ->chunk(1000, function ($rows) {
                        $payload = [];
                        foreach ($rows as $r) {
                            $payload[] = [
                                'product_id' => $r->product_id,
                                'name' => $r->name ?? null,
                                'diamonds' => $r->diamonds ?? '0',
                                'price' => (int) ($r->price ?? 0),
                                'category' => $r->category ?? null,
                                'region' => $r->region ?? 'myanmar',
                                'status' => isset($r->status) ? (int) $r->status : 1,
                                'created_at' => $r->created_at ?? now(),
                                'updated_at' => $r->updated_at ?? now(),
                            ];
                        }
                        if (! empty($payload)) {
                            DB::table('usermlproducts')->insert($payload);
                        }
                    });
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('usermlproducts')) {
            Schema::drop('usermlproducts');
        }

        if (Schema::hasTable('adminmlproducts') && ! Schema::hasTable('mlproducts')) {
            Schema::rename('adminmlproducts', 'mlproducts');
        }
    }
};
