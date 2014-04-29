<?php

class TestdataSeeder extends Seeder {

	public function run()
	{
		$user_sentry = Sentry::findUserByLogin('tester@example.com');
		$user_kakadu = User::find($user_sentry->getId());
		$role = Role::where('name', 'LIKE', 'admin')->first();
		$group = new Learngroup();
		$group->name = 'Group Tester';
		$group->description = 'Group for Testing';
		$group->save();
		
		//Add admin
		//$group->users()->attach($user_kakadu, array('role_id' => $role->id));

		//Create Course for Testing
		$catalog = new Catalog();
		$catalog->name = 'General Knowledge';
		$catalog->number = 1;
		$catalog->save();

		//5 Simple Questions
		$q = array(
            'question' => 'Was ist die Hauptstadt von Albanien?'
            );

        $a = array(
            'answer'    => 'Tirana'
            );

        $question0 = new Question();
        $question0->type = 'simple';
        $question0->question = json_encode($q);
        $question0->answer = json_encode($a);
        $question0->save();

        $q = array(
            'question' => 'Was ist die Hauptstadt von Bosnien und Herzegowina?'
            );

        $a = array(
            'answer'    => 'Sarajevo'
            );

        $question1 = new Question();
        $question1->type = 'simple';
        $question1->question = json_encode($q);
        $question1->answer = json_encode($a);
        $question0->save();
        
        $q = array(
            'question' => 'Was ist die Hauptstadt von Griechenland?'
            );

        $a = array(
            'answer'    => 'Athen'
            );

        $question2 = new Question();
        $question2->type = 'simple';
        $question2->question = json_encode($q);
        $question2->answer = json_encode($a);
        $question2->save();
        
        $q = array(
            'question' => 'Was ist die Hauptstadt von Mazedonien?'
            );

        $a = array(
            'answer'    => 'Skopje'
            );

        $question3 = new Question();
        $question3->type = 'simple';
        $question3->question = json_encode($q);
        $question3->answer = json_encode($a);
        $question3->save();
        
        $q = array(
            'question' => 'Was ist die Hauptstadt von San Marino?'
            );

        $a = array(
            'answer'    => 'San Marino'
            );

        $question4 = new Question();
        $question4->type = 'simple';
        $question4->question = json_encode($q);
        $question4->answer = json_encode($a);
        $question4->save();

		//5 multiple question
        $q = array(
            'question' => 'Wann wurde das Euro-Bargeld eingeführt?'
            );

        $a = array(
            'answer'    => array(
                '2'
                ),
            'choices'   => array(
                '01.01.2000',
                '31.12.2001',
                '01.01.2002',
                '01.01.2010'
                )
            );

        $question5 = new Question();
        $question5->type = 'multiple';
        $question5->question = json_encode($q);
        $question5->answer = json_encode($a);
        $question5->save();

        $q = array(
            'question' => 'Was versteht man unter Phishing?'
            );

        $a = array(
            'answer'    => array(
                '0'
                ),
            'choices'   => array(
                'Datenklau',
                'Spam-Mails',
                'Datenschutz',
                'Virenprogramme'
                )
            );

        $question6 = new Question();
        $question6->type = 'multiple';
        $question6->question = json_encode($q);
        $question6->answer = json_encode($a);
        $question6->save();

        $q = array(
            'question' => 'Wie oft wurde Michael Schumacher Formel-1-Weltmeister?'
            );

        $a = array(
            'answer'    => array(
                '1'
                ),
            'choices'   => array(
                '10',
                '7',
                '3',
                '4'
                )
            );

        $question7 = new Question();
        $question7->type = 'multiple';
        $question7->question = json_encode($q);
        $question7->answer = json_encode($a);
        $question7->save();

        $q = array(
            'question' => 'Welche Farbe haben die Torstangen im Eishockey?'
            );

        $a = array(
            'answer'    => array(
                '0'
                ),
            'choices'   => array(
                'rot',
                'blau',
                'schwarz'
                )
            );

        $question8 = new Question();
        $question8->type = 'multiple';
        $question8->question = json_encode($q);
        $question8->answer = json_encode($a);
        $question8->save();

        $q = array(
            'question' => 'Wer hat den Satz I will be back gesagt?'
            );

        $a = array(
            'answer'    => array(
                '1'
                ),
            'choices'   => array(
                'Sylvester Stallone',
                'Arnold Schwarzenegger',
                'Vin Diesel',
                'Peter Fox'
                )
            );

        $question9 = new Question();
        $question9->type = 'multiple';
        $question9->question = json_encode($q);
        $question9->answer = json_encode($a);
        $question9->save();

        $q = array(
            'question'  => '1963 gründeten Diana Ross, Florence Ballard und Mary Wilson die Band The Supremes. Sie waren ein weibliches US-amerikanisches Pop/Soul-Gesangstrio der 1960er und 70er Jahre. Aufgrund seiner erfolgreichen Karriere wurde es 1988 in die Rock and Roll Hall of Fame aufgenommen.'
            );

        $a = array(
            'answer'   => array(
                'The Supremes'
                )
            );
        $question10 = new Question();
        $question10->type = 'cloze';
        $question10->question = json_encode($q);
        $question10->answer = json_encode($a);
        $question10->save();

        $q = array(
            'question'  => 'Sokrates ging es um die Fragen der Ethik, um sittliches Wissen, um die Unterscheidung von Gut und Böse. Er glaubte an die menschliche Vernunft sowie an klare und allgemeingültige Regeln für Recht und Unrecht. Selbsterkenntnis sei die Aufgabe des Einzelnen: Wer Rechenschaft über sich und sein Leben ablege, könne schließlich erkennen, wie man sich verhalten muss, um zum wahren Menschen zu werden. Alle Laster beruhen seiner Meinung nach auf Unwissenheit, nicht auf absichtlicher Bösartigkeit. Rechtes Denken führt nach Sokrates zu rechtem Handeln.'
            );

        $a = array(
            'answer'   => array(
                'Sokrates',
                'Selbsterkenntnis',
                'Regeln'
                )
            );
        $question11 = new Question();
        $question11->type = 'cloze';
        $question11->question = json_encode($q);
        $question11->answer = json_encode($a);
        $question11->save();

        $q = array(
            'question'  => 'Am 1. Weihnachtsfeiertag des Jahres 1952 nahm der Nordwestdeutsche Rundfunk offiziell seinen Sendebetrieb auf. Das Programm dauerte täglich zweieinhalb Stunden und bestand überwiegend aus Unterhaltungssendungen. Die Geschichte des Fernsehens ist allerdings viel älter. Bereits 1884 erfand Paul Nipkow den elektrischen Teleskopen. In Deutschland verfolgten 1936 die Zuschauer in Fernsehstuben die Olympischen Spiele. Die Nazis manipulierten auch an der Front das Fernsehen für ihre Zwecke und hielten die Soldaten mit großen Musikrevuen bei Laune'
            );

        $a = array(
            'answer'   => array(
                '1952',
                '1884',
                '1936'
                )
            );
        $question12 = new Question();
        $question12->type = 'cloze';
        $question12->question = json_encode($q);
        $question12->answer = json_encode($a);
        $question12->save();

        $q = array(
            'question'  => 'Papyrus ist empfindlich gegen mechanische Beanspruchung, Feuchtigkeit und Wurmfraß, weist aber grundsätzlich eine erstaunlich hohe Haltbarkeit auf. Plinius erwähnt sein Studium einer 300 Jahre alten Papyrusrolle. Bis in die Gegenwart sind Papyri nur im trockenen Wüstensand Nordafrikas (vor allem Ägyptens) und des Vorderen Orients erhalten geblieben. In Ägypten sind vom 3. Jahrhundert v. Chr. bis in die römische Kaiserzeit Papyri auch zu Kartonagen verklebt worden, die für die Umhüllung von Mumien verwendet wurden. Durch Auflösung der Kartonagen können die Texte auf den Papyri wieder lesbar gemacht werden.'
            );

        $a = array(
            'answer'   => array(
                'Papyrus',
                'verklebt',
                'Kartonagen',
                'Feuchtigkeit'
                )
            );
        $question13 = new Question();
        $question13->type = 'cloze';
        $question13->question = json_encode($q);
        $question13->answer = json_encode($a);
        $question13->save();

        $q = array(
            'question'  => 'Für die weltweite Imkerei hat die Westliche Honigbiene die größte Bedeutung; in vielen asiatischen Ländern wird auch die dort ursprünglich vorkommende Östliche Honigbiene in einfachen Klotzbeuten oder Höhlungen von Mauern gehalten. Diese beiden Arten brüten im Schutz von Höhlen und konnten sich dadurch sehr weit aus den tropischen Regionen heraus in gemäßigtere Klimazonen ausbreiten, wodurch sich insbesondere bei der Westlichen Honigbiene regional verschiedene Bienenrassen herausgebildet haben. Eine natürliche Grenze der Besiedelung wird oft durch Gehölze gebildet.'
            );

        $a = array(
            'answer'   => array(
                'Honigbiene',
                'Höhlungen',
                'Bienenrassen'
                )
            );
        $question14 = new Question();
        $question14->type = 'cloze';
        $question14->question = json_encode($q);
        $question14->answer = json_encode($a);
        $question14->save();

        $q = array(
            'question'  => 'Was ist ein Easter Egg?'
            );

        $a = array(
            'answer'    => 'Eine versteckte Signatur',
            'choices'   => array(
                'Eine versteckte Signatur',
                'Ein virtuelles Osterei',
                'In Computerprogrammen versteckte Bilder',
                'Ein Emoticon'
                )
            );

        $question15 = new Question();
        $question15->type = 'dragdrop';
        $question15->question = json_encode($q);
        $question15->answer = json_encode($a);
        $question15->save();

        $q = array(
            'question'  => 'Was waren die ersten Worte am Telefon?'
            );

        $a = array(
            'answer'    => 'Mr. Watson - come here',
            'choices'   => array(
                'Mr. Watson - come here',
                'Hello',
                'Ahoy',
                'Great it works'
                )
            );

        $question16 = new Question();
        $question16->type = 'dragdrop';
        $question16->question = json_encode($q);
        $question16->answer = json_encode($a);
        $question16->save();

        $q = array(
            'question'  => 'In welchem Land der Erde wurden die größten Temperaturunterschiede gemessen?'
            );

        $a = array(
            'answer'    => 'Russland',
            'choices'   => array(
                'Russland',
                'Finnland',
                'Amerika',
                'China'
                )
            );

        $question17 = new Question();
        $question17->type = 'dragdrop';
        $question17->question = json_encode($q);
        $question17->answer = json_encode($a);
        $question17->save();

        $q = array(
            'question'  => 'Was bedeutet Altruismus?'
            );

        $a = array(
            'answer'    => 'Selbstlosigkeit',
            'choices'   => array(
                'Selbstlosigkeit',
                'Selbstgefällig',
                'Emotionslosigkeit',
                'Missgunst'
                )
            );

        $question18 = new Question();
        $question18->type = 'dragdrop';
        $question18->question = json_encode($q);
        $question18->answer = json_encode($a);
        $question18->save();

        $q = array(
            'question'  => 'Wie groß ist das Spielfeld beim Basketball?'
            );

        $a = array(
            'answer'    => '26x14m',
            'choices'   => array(
                '25x14m',
                '26x14m',
                '26x13m',
                '26x15m'
                )
            );

        $question19 = new Question();
        $question19->type = 'dragdrop';
        $question19->question = json_encode($q);
        $question19->answer = json_encode($a);
        $question19->save();

        $catalog->questions()->save($question0);
        $catalog->questions()->save($question1);
        $catalog->questions()->save($question2);
        $catalog->questions()->save($question3);
        $catalog->questions()->save($question4);
        $catalog->questions()->save($question5);
        $catalog->questions()->save($question6);
        $catalog->questions()->save($question7);
        $catalog->questions()->save($question8);
        $catalog->questions()->save($question9);
        $catalog->questions()->save($question10);
        $catalog->questions()->save($question11);
        $catalog->questions()->save($question12);
        $catalog->questions()->save($question13);
        $catalog->questions()->save($question14);
        $catalog->questions()->save($question15);
        $catalog->questions()->save($question16);
        $catalog->questions()->save($question17);
        $catalog->questions()->save($question18);
        $catalog->questions()->save($question19);

        $course = new Course();
        $course->name = 'General Knowledge';
        $course->description = 'General Knowledge';
        $course->catalog = $catalog->id;
        $course->save();

        $course->learngroups()->save($group);
    }

}