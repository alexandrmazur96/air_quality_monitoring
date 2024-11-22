<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Mazur\Models\City;

return new class extends Migration
{
    public function up(): void
    {
        $names = [
            'Odesa' => 'Odessa',
            'Zaporizhzhya' => 'Zaporizhia',
            'Kryvyy Rih' => 'Kryvyi Rih',
            'Staryy Krym' => 'Stary Krym',
            'Kirovohrad' => 'Kropyvnytskyi',
            'Kulynychi' => 'Kharkiv',
            'Rozkishne' => 'Luhansk',
            'Akhiar' => 'Sevastopol',
            'Kamelnitskiy' => 'Khmelnytskyi',
            'Dobroye' => 'Simferopol',
            'Petrykiv' => 'Ternopil',
            'Uhryniv' => 'Ivano-Frankivsk',
            'Krasnotorka' => 'Kramators\'k',
            'Vodyane' => 'Pokrovsk',
            'Osypenko' => 'Berdyansk',
            'Krasnyy Oktyabr' => 'Yenakiyeve',
            'Aleksandriya' => 'Oleksandriya', // {"latitude":48.67,"longitude":33.121}
            'Braga' => 'Kamianets-Podilskyi', // {"latitude":48.517,"longitude":26.5}
            'Verkhnyachka' => 'Uman', // {"latitude":48.831,"longitude":30.037}
            'Stakhanov' => 'Kadiyivka', // {"latitude":48.563,"longitude":38.651}
            'Belz' => 'Zhovkva', // {"latitude":50.383,"longitude":24.017}
            'Artemovsk' => 'Bakhmut', // {"latitude":48.591,"longitude":38.008}
            'Rykhtychi' => 'Drohobych', // {"latitude":49.383,"longitude":23.55}
            'Torez' => 'Chystyakove', // {"latitude":48.038,"longitude":38.588}
            'Krasnoarmeysk' => 'Pokrovsk', // {"latitude":48.283,"longitude":37.183}
            'Klenovyy' => 'Roven\'ky', // {"latitude":48.118,"longitude":39.457}
            'Korosten' => 'Korosten\'', // {"latitude":50.95,"longitude":28.65}
            'Lesnoye' => 'Nyzhnya Krynka', // {"latitude":48.1,"longitude":38.15}
            'Kolomyya' => 'Kolomyia', // {"latitude":48.531,"longitude":25.04}
            'Stryy' => 'Stryi', // {"latitude":49.25,"longitude":23.85}
            'Zhovtneve' => 'Stryi', // {"latitude":50.65,"longitude":24.267}
            'Nowograd Wolynsk' => 'Rivne', // {"latitude":50.6,"longitude":27.6167},
            'Dniprovka' => 'Energodar', // {"latitude":47.432,"longitude":34.621}
            'Trebukhiv' => 'Brovary', // {"latitude":50.483,"longitude":30.9}
            'Chervonohryhorivka' => 'Marhanets\'', // {"latitude":47.625,"longitude":34.54}
            'Shabo' => 'Bilhorod-Dnistrovskyi', // {"latitude":46.133,"longitude":30.383}
            'Krasnodon' => 'Sorokyne', // {"latitude":48.295,"longitude":39.74}
            'Yablonki' => 'Irpin', // {"latitude":50.55,"longitude":30.2167}
            'Wosnessensk' => 'Voznesensk', // {"latitude":47.55,"longitude":31.3333}
            'Dzerzhinsk' => 'Horlivka', // {"latitude":48.405,"longitude":37.833}
            'Pervomaysk' => 'Pervomays\'k', // {"latitude":48.628,"longitude":38.556}
            'Vladimir' => 'Volodymyr-Volynskyi', // {"latitude":50.85,"longitude":24.3333}
            'Tsybli' => 'Pereiaslav-Khmelnytskyi', // {"latitude":49.967,"longitude":31.55}
            'Gigant' => 'Dobropillya', // {"latitude":48.4667,"longitude":37.1}
            'Molodizhne' => 'Dolyns\'ka', // {"latitude":48.179,"longitude":32.659}
            'Starokostyantyniv' => 'Starokostiantyniv', // {"latitude":49.75,"longitude":27.217}
            'Serebriya' => 'Mohyliv-Podilskyi', // {"latitude":48.467,"longitude":27.7}
            'Sinelnikovo' => 'Synel\'nykove', // {"latitude":48.3333,"longitude":35.5167}
            'Voyinka' => 'Krasnoperekops\'k', // {"latitude":45.868,"longitude":33.993}
            'Kostopel' => 'Kostopil\'', // {"latitude":50.8833,"longitude":26.45}
            'Beloye' => 'Alchevs\'k', // {"latitude":48.493,"longitude":39.036}
            'Shklo' => 'Novoyavorivs\'k', // {"latitude":49.95,"longitude":23.533}
            'Saky' => 'Saki', // {"latitude":45.136,"longitude":33.603}
            'Dmytrivka' => 'Znomenka', // {"latitude":48.803,"longitude":32.723}
            'Krasny Liman' => 'Pokrovsk', // {"latitude":48.9833,"longitude":37.8167}
            'Pidvynohradiv' => 'Vynohradiv', // {"latitude":48.133,"longitude":22.967}
            'Bakhchysaray' => 'Bakhchysarai', // {"latitude":44.75,"longitude":33.867}
            'Haysyn' => 'Haisyn', // {"latitude":48.8,"longitude":29.4}
            'Gornyak' => 'Mar\'yinka', // {"latitude":48.067,"longitude":37.367}
            'Korolevets' => 'Krolevets\'', // {"latitude":51.55,"longitude":33.3833}
            'Dokuchayevsk' => 'Dokuchayevs\'k', // {"latitude":47.7333,"longitude":37.6667}
            'Staraya Belaya' => 'Starobil\'s\'k', // {"latitude":49.2764,"longitude":38.9058}
            'Spas' => 'Dolyna', // {"latitude":48.888,"longitude":24.059}
            'Balky' => 'Dniprorudne', // {"latitude":47.383,"longitude":34.951}
            'Putivl' => 'Putyvl\'', // {"latitude":51.3333,"longitude":33.8667}
            'Volchansk' => 'Vovchans\'k', // {"latitude":50.288,"longitude":36.942}
            'Krasne' => 'Skadovs\'k', // {"latitude":46.117,"longitude":32.783}
            'Zvenyhorodka' => 'Zvenihorodka', // {"latitude":49.083,"longitude":30.967}
            'Korsunshevchenkivskyy' => 'Korsun-Shevchenkivskyi', // {"latitude":49.4333,"longitude":31.25}
            'Prigorodnoye' => 'Balaklava', // {"latitude":44.5167,"longitude":33.6}
            'Rozdilna' => 'Rozdil\'na', // {"latitude":46.85,"longitude":30.0833}
            'Biryukove' => 'Voznesensk', // {"latitude":47.958,"longitude":39.737}
            'Vapnyarka' => 'Kryzhopil\'', // {"latitude":48.533,"longitude":28.767}
            'Komyshany' => 'Kherson', // {"latitude":46.636,"longitude":32.508}
            'Sukhovolya' => 'Lviv', // {"latitude":49.817,"longitude":23.833}
            'Dunayivtsi' => 'Dunaivtsi', // {"latitude":48.9,"longitude":26.833}
            'Radomishl' => 'Radomyshl', // {"latitude":50.5,"longitude":29.2333}
            'Tetiyiv' => 'Tetiiv', // {"latitude":49.383,"longitude":29.667}
            'Yama' => 'Sivers\'k', // {"latitude":48.8636,"longitude":38.0992}
            'Prymorsk' => 'Prymors\'k', // {"latitude":46.7294,"longitude":36.3514}
            'Schastye' => 'Shchastya', // {"latitude":48.7406,"longitude":39.23}
            'Ukrayinka' => 'Ukrainka', // {"latitude":50.133,"longitude":30.733}
            'Stanichnnoluganskoye' => 'Stanytsya Luhans\'ka', // {"latitude":48.6511,"longitude":39.4861}
            'Kotelva' => 'Kotel\'va', // {"latitude":50.0703,"longitude":34.7539}
            'Rodnykove' => 'Gresovskiy', // {"latitude":45.05,"longitude":33.95}
            'Polyanka' => 'Baranivka', // {"latitude":50.25,"longitude":27.7}
            'Syedove' => 'Novoazovs\'k', // {"latitude":47.075,"longitude":38.159}
            'Borodyanka' => 'Borodianka', // {"latitude":50.65,"longitude":29.983}
            'Sloboda' => 'Bilopillya', // {"latitude":51.183,"longitude":33.617}
            'Toshkivka' => 'Hirs\'ke', // {"latitude":48.776,"longitude":38.572}
            'Kamenkashirsk' => 'Kamin-Kashyrskyi', // {"latitude":51.6333,"longitude":24.9667}
            'Sudova Vyshnya' => 'Mostys\'ka', // {"latitude":49.783,"longitude":23.367}
            'Lenine' => 'Shchyolkino', // {"latitude":45.3,"longitude":35.783}
            'Zhelyabovka' => 'Sovetskiy', // {"latitude":45.4,"longitude":34.75}
            'Zinkiv' => 'Zin\'kiv', // {"latitude":50.2119,"longitude":34.3567}
            'Kurpaty' => 'Gaspra', // {"latitude":44.433,"longitude":34.117}
            'Chodorov' => 'Khodoriv', // {"latitude":49.4,"longitude":24.3167}
            'Snyatyn' => 'Sniatyn', // {"latitude":48.45,"longitude":25.567}
            'Chernyakhiv' => 'Cherniakhiv', // {"latitude":50.45,"longitude":28.667}
            'Luboml' => 'Liuboml', // {"latitude":51.2333,"longitude":24.0333}
            'Beresowka' => 'Berezivka', // {"latitude":47.2167,"longitude":30.9167}
            'Rafno' => 'Ratne', // {"latitude":51.6667,"longitude":24.5167}
        ];

        $cities = DB::table('cities')
            ->select('id', 'name')
            ->get()
            ->mapWithKeys(static fn(stdClass $city): array => [md5($city->name) => $city->id]);
        DB::beginTransaction();
        foreach ($names as $alias => $cityName) {
            DB::table('cities_alias')
                ->insert([
                    'alias' => $alias,
                    'city_id' => $cities[md5($cityName)],
                ]);
        }
        DB::commit();
    }
};
