<?php

use App\Models\Sticker;
use MotorORM\Migration;

return new class
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $migration = new Migration(new Sticker());
        $migration->createTable(function (Migration $table) {
            $table->create('code');
            $table->create('path');
        });

        foreach ($this->seed() as $seed) {
            Sticker::query()->create($seed);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $migration = new Migration(new Sticker());
        $migration->deleteTable();
    }

    /**
     * Seed
     *
     * @return array[]
     */
    private function seed(): array
    {
        return [
            ['code' => ':)', 'path' => 'uploads/stickers/smile.gif'],
            ['code' => ':(', 'path' => 'uploads/stickers/sad.gif'],
            ['code' => ':4moks', 'path' => 'uploads/stickers/4moks.gif'],
            ['code' => ':D', 'path' => 'uploads/stickers/D.gif'],
            ['code' => ':E', 'path' => 'uploads/stickers/E.gif'],
            ['code' => ':aaa', 'path' => 'uploads/stickers/aaa.gif'],
            ['code' => ':agree', 'path' => 'uploads/stickers/agree.gif'],
            ['code' => ':airkiss', 'path' => 'uploads/stickers/airkiss.gif'],
            ['code' => ':atlet', 'path' => 'uploads/stickers/atlet.gif'],
            ['code' => ':baby', 'path' => 'uploads/stickers/baby.gif'],
            ['code' => ':bant', 'path' => 'uploads/stickers/bant.gif'],
            ['code' => ':be', 'path' => 'uploads/stickers/be.gif'],
            ['code' => ':blin', 'path' => 'uploads/stickers/blin.gif'],
            ['code' => ':blum', 'path' => 'uploads/stickers/blum.gif'],
            ['code' => ':bomba', 'path' => 'uploads/stickers/bomba.gif'],
            ['code' => ':bounce', 'path' => 'uploads/stickers/bounce.gif'],
            ['code' => ':bugaga', 'path' => 'uploads/stickers/bugaga.gif'],
            ['code' => ':buhoj', 'path' => 'uploads/stickers/buhoj.gif'],
            ['code' => ':bwink', 'path' => 'uploads/stickers/bwink.gif'],
            ['code' => ':cold', 'path' => 'uploads/stickers/cold.gif'],
            ['code' => ':cool', 'path' => 'uploads/stickers/cool.gif'],
            ['code' => ':cry', 'path' => 'uploads/stickers/cry.gif'],
            ['code' => ':ded', 'path' => 'uploads/stickers/ded.gif'],
            ['code' => ':derisive', 'path' => 'uploads/stickers/derisive.gif'],
            ['code' => ':drool', 'path' => 'uploads/stickers/drool.gif'],
            ['code' => ':duma', 'path' => 'uploads/stickers/duma.gif'],
            ['code' => ':exercise', 'path' => 'uploads/stickers/exercise.gif'],
            ['code' => ':faq', 'path' => 'uploads/stickers/faq.gif'],
            ['code' => ':fermer', 'path' => 'uploads/stickers/fermer.gif'],
            ['code' => ':fingal', 'path' => 'uploads/stickers/fingal.gif'],
            ['code' => ':flirt', 'path' => 'uploads/stickers/flirt.gif'],
            ['code' => ':fuck', 'path' => 'uploads/stickers/fuck.gif'],
            ['code' => ':girl_blum', 'path' => 'uploads/stickers/girl_blum.gif'],
            ['code' => ':girl_bye', 'path' => 'uploads/stickers/girl_bye.gif'],
            ['code' => ':girl_cry', 'path' => 'uploads/stickers/girl_cry.gif'],
            ['code' => ':girl_hide', 'path' => 'uploads/stickers/girl_hide.gif'],
            ['code' => ':girl_wink', 'path' => 'uploads/stickers/girl_wink.gif'],
            ['code' => ':girls', 'path' => 'uploads/stickers/girls.gif'],
            ['code' => ':happy', 'path' => 'uploads/stickers/happy.gif'],
            ['code' => ':heart', 'path' => 'uploads/stickers/heart.gif'],
            ['code' => ':hello', 'path' => 'uploads/stickers/hello.gif'],
            ['code' => ':help', 'path' => 'uploads/stickers/help.gif'],
            ['code' => ':help2', 'path' => 'uploads/stickers/help2.gif'],
            ['code' => ':hi', 'path' => 'uploads/stickers/hi.gif'],
            ['code' => ':infat', 'path' => 'uploads/stickers/infat.gif'],
            ['code' => ':kiss', 'path' => 'uploads/stickers/kiss.gif'],
            ['code' => ':kiss2', 'path' => 'uploads/stickers/kiss2.gif'],
            ['code' => ':klass', 'path' => 'uploads/stickers/klass.gif'],
            ['code' => ':krut', 'path' => 'uploads/stickers/krut.gif'],
            ['code' => ':krutoy', 'path' => 'uploads/stickers/krutoy.gif'],
            ['code' => ':ku', 'path' => 'uploads/stickers/ku.gif'],
            ['code' => ':kuku', 'path' => 'uploads/stickers/kuku.gif'],
            ['code' => ':kulak', 'path' => 'uploads/stickers/kulak.gif'],
            ['code' => ':lamer', 'path' => 'uploads/stickers/lamer.gif'],
            ['code' => ':love', 'path' => 'uploads/stickers/love.gif'],
            ['code' => ':love2', 'path' => 'uploads/stickers/love2.gif'],
            ['code' => ':mail', 'path' => 'uploads/stickers/mail.gif'],
            ['code' => ':mister', 'path' => 'uploads/stickers/mister.gif'],
            ['code' => ':money', 'path' => 'uploads/stickers/money.gif'],
            ['code' => ':moped', 'path' => 'uploads/stickers/moped.gif'],
            ['code' => ':musik', 'path' => 'uploads/stickers/musik.gif'],
            ['code' => ':nea', 'path' => 'uploads/stickers/nea.gif'],
            ['code' => ':net', 'path' => 'uploads/stickers/net.gif'],
            ['code' => ':neznaju', 'path' => 'uploads/stickers/neznaju.gif'],
            ['code' => ':ninja', 'path' => 'uploads/stickers/ninja.gif'],
            ['code' => ':no', 'path' => 'uploads/stickers/no.gif'],
            ['code' => ':nono', 'path' => 'uploads/stickers/nono.gif'],
            ['code' => ':nozh', 'path' => 'uploads/stickers/nozh.gif'],
            ['code' => ':nyam', 'path' => 'uploads/stickers/nyam.gif'],
            ['code' => ':icecream', 'path' => 'uploads/stickers/nyam2.gif'],
            ['code' => ':obana', 'path' => 'uploads/stickers/obana.gif'],
            ['code' => ':ogogo', 'path' => 'uploads/stickers/ogogo.gif'],
            ['code' => ':oops', 'path' => 'uploads/stickers/oops.gif'],
            ['code' => ':opa', 'path' => 'uploads/stickers/opa.gif'],
            ['code' => ':otstoy', 'path' => 'uploads/stickers/otstoy.gif'],
            ['code' => ':oy', 'path' => 'uploads/stickers/oy.gif'],
            ['code' => ':pirat', 'path' => 'uploads/stickers/pirat.gif'],
            ['code' => ':pirat2', 'path' => 'uploads/stickers/pirat2.gif'],
            ['code' => ':pistolet', 'path' => 'uploads/stickers/pistolet.gif'],
            ['code' => ':pistolet2', 'path' => 'uploads/stickers/pistolet2.gif'],
            ['code' => ':shok3', 'path' => 'uploads/stickers/pizdec.gif'],
            ['code' => ':poisk', 'path' => 'uploads/stickers/poisk.gif'],
            ['code' => ':proud', 'path' => 'uploads/stickers/proud.gif'],
            ['code' => ':puls', 'path' => 'uploads/stickers/puls.gif'],
            ['code' => ':queen', 'path' => 'uploads/stickers/queen.gif'],
            ['code' => ':rap', 'path' => 'uploads/stickers/rap.gif'],
            ['code' => ':read', 'path' => 'uploads/stickers/read.gif'],
            ['code' => ':respekt', 'path' => 'uploads/stickers/respekt.gif'],
            ['code' => ':rok', 'path' => 'uploads/stickers/rok.gif'],
            ['code' => ':rok2', 'path' => 'uploads/stickers/rok2.gif'],
            ['code' => ':senjor', 'path' => 'uploads/stickers/senjor.gif'],
            ['code' => ':shok', 'path' => 'uploads/stickers/shok.gif'],
            ['code' => ':shok2', 'path' => 'uploads/stickers/shok2.gif'],
            ['code' => ':skull', 'path' => 'uploads/stickers/skull.gif'],
            ['code' => ':smert', 'path' => 'uploads/stickers/smert.gif'],
            ['code' => ':smoke', 'path' => 'uploads/stickers/smoke.gif'],
            ['code' => ':spy', 'path' => 'uploads/stickers/spy.gif'],
            ['code' => ':strela', 'path' => 'uploads/stickers/strela.gif'],
            ['code' => ':svist', 'path' => 'uploads/stickers/svist.gif'],
            ['code' => ':tiho', 'path' => 'uploads/stickers/tiho.gif'],
            ['code' => ':vau', 'path' => 'uploads/stickers/vau.gif'],
            ['code' => ':victory', 'path' => 'uploads/stickers/victory.gif'],
            ['code' => ':visavi', 'path' => 'uploads/stickers/visavi.gif'],
            ['code' => ':visavi2', 'path' => 'uploads/stickers/visavi2.gif'],
            ['code' => ':vtopku', 'path' => 'uploads/stickers/vtopku.gif'],
            ['code' => ':wackogirl', 'path' => 'uploads/stickers/wackogirl.gif'],
            ['code' => ':xaxa', 'path' => 'uploads/stickers/xaxa.gif'],
            ['code' => ':xmm', 'path' => 'uploads/stickers/xmm.gif'],
            ['code' => ':yu', 'path' => 'uploads/stickers/yu.gif'],
            ['code' => ':zlo', 'path' => 'uploads/stickers/zlo.gif'],
            ['code' => ':ban', 'path' => 'uploads/stickers/ban.gif'],
            ['code' => ':ban2', 'path' => 'uploads/stickers/ban2.gif'],
            ['code' => ':banned', 'path' => 'uploads/stickers/banned.gif'],
            ['code' => ':closed', 'path' => 'uploads/stickers/closed.gif'],
            ['code' => ':closed2', 'path' => 'uploads/stickers/closed2.gif'],
            ['code' => ':devil', 'path' => 'uploads/stickers/devil.gif'],
            ['code' => ':flood', 'path' => 'uploads/stickers/flood.gif'],
            ['code' => ':flood2', 'path' => 'uploads/stickers/flood2.gif'],
            ['code' => ':huligan', 'path' => 'uploads/stickers/huligan.gif'],
            ['code' => ':ment', 'path' => 'uploads/stickers/ment.gif'],
            ['code' => ':ment2', 'path' => 'uploads/stickers/ment2.gif'],
            ['code' => ':moder', 'path' => 'uploads/stickers/moder.gif'],
            ['code' => ':girlmoder', 'path' => 'uploads/stickers/nika.gif'],
            ['code' => ':offtop', 'path' => 'uploads/stickers/offtop.gif'],
            ['code' => ':pravila', 'path' => 'uploads/stickers/pravila.gif'],
            ['code' => ':zona', 'path' => 'uploads/stickers/zona.gif'],
            ['code' => ':zub', 'path' => 'uploads/stickers/zub.gif'],
            ['code' => ':crazy', 'path' => 'uploads/stickers/crazy.gif'],
            ['code' => ':moder2', 'path' => 'uploads/stickers/paratrooper.gif'],
            ['code' => ':bug', 'path' => 'uploads/stickers/bug.gif'],
            ['code' => ':wall', 'path' => 'uploads/stickers/wall.gif'],
            ['code' => ':boss', 'path' => 'uploads/stickers/boss.gif'],
            ['code' => ':facepalm', 'path' => 'uploads/stickers/facepalm.gif'],
        ];
    }
};
