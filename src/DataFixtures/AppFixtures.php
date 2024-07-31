<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Project;
use App\Entity\ProjectMedia;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    private UserPasswordHasherInterface $hasher;

    public function __Construct(UserPasswordHasherInterface $hasher){

        $this->hasher = $hasher;

    }
 
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 5; $i++){

            $unPubArticle[$i] = new Article();
            $unPubArticle[$i]->setTitle('Article non publié n° '.($i+1));
            $unPubArticle[$i]->setContent('Le <b>Lorem Ipsum</b> est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l\'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n\'a pas fait que survivre cinq siècles, mais s\'est aussi adapté à la bureautique informatique, sans que son contenu n\'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.');
            $unPubArticle[$i]->setIsPublished(false);
            $manager->persist($unPubArticle[$i]);


            $pubArticle[$i] = new Article();
            $pubArticle[$i]->setTitle('Article publié n° '.($i+1));
            $pubArticle[$i]->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed euismod eget nisi sed blandit. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Donec ut leo risus. Ut at sollicitudin turpis. Donec aliquet molestie ligula, quis viverra ligula. Lorem ipsum dolor sit amet, consectetur adipiscing elit. In at orci non augue pretium molestie. Suspendisse potenti. Nulla quis lorem consequat, lobortis nunc eu, luctus sapien. Sed consequat nunc vel purus consectetur, fermentum pulvinar enim laoreet. Duis efficitur enim vel nibh tempor, ac accumsan mi aliquam. Sed dapibus vestibulum libero, eu suscipit augue posuere ac. Pellentesque at interdum elit.');
            $pubArticle[$i]->setIsPublished(true);
            $manager->persist($pubArticle[$i]);


        }
        
        $utilisateur = new User();
        $utilisateur->setFirstname('Emile');
        $utilisateur->setName('Util');
        $utilisateur->setEmail('user@emile.blog');
        $utilisateur->setPassword($this->hasher->hashPassword($utilisateur, 'MotDePasse1234'));
        $utilisateur->setRoles(['ROLE_USER']);
        $manager->persist($utilisateur);

        $admin = new User();
        $admin->setFirstname('Emile');
        $admin->setName('Admin');
        $admin->setEmail('admin@emile.blog');
        $admin->setPassword($this->hasher->hashPassword($admin, 'MotDePasse1234'));
        $admin->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $manager->persist($admin);

        $media = new ProjectMedia();
        $media->setName('icone-p6.png');
        
        $ocrSix = new Project();
        $ocrSix->setTitle('OCR 6');
        $ocrSix->setGithubLink('https://github.com/Emile31500/OCR_6');
        $ocrSix->setContent('<h2>Context</h2><p><br>Ce projet a été réalisé dans le cadre de ma formation OpenClassrooms.</br>Jimmy Sweat est un entrepreneur qui souhaite créer un site communautaire pour faire découvrir le snowboard et aider les novices à apprendre les différentes figures.</br></br>Le site devait être réalisé avec Symfony. Les seuls codes externes autorisés étaient Bootstrap et les bibliothèques de Composer. Des wireframes à respecter étaient également fournis par OCR.</br></p><h1>Information pour l\'instalation :</h1><h2>Importer le projet</h2><p>Ouvrez votre terminal de commande en mode sudo et éxecutez la commande suivante pour téléchargez le projet :<code>$ git clone https://github.com/Emile31500/OCR_6_Developpez-de-A-a-Z-le-site-communautaire-SnowTricks</code></p><h2>Configuration de la Base de Données</h2><p><br>Dans le fichier : ../root/.env</br></br>App : nom d\'utilisateur de la base de données</br>!ChangeME! : mot de passe de cet utilisateur</br>127.0.0.1:3306 : addresse et port utilisé par votre BDD</br>app : le nom de la base de données (ici snow_tricks)</br></p><pre><code> # DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8.0.32&charset=utf8mb4"</code></pre><p>Créez une base de données que vous appellerez snow_trikcsImporter la base de données dans le dossier suivant <code>../root/Diagrammes/SnowTricks.sql</code>Démarrez le serveur web avec:<code>$ php -S 127.0.0.1:8100 -t public</code></p><h2>À savoir</h2><p><span style="color: rgb(255, 0, 0)">La récupération de mot de passe ne fonctionne pas tant que le site ne sera pas définitivement déployé : </span><br>Lien de modificaton est normalement</br><code>ip.server:port/modifier-le-mot-de-passe/{mot_de_passe}</code></br>On est obligé d\'envoyer une addresse absolue, par défaut on envoie donc</br><code>127.0.0.1:8100/modifier-le-mot-de-passe/{mot_de_passe}</code> </br><b>Lorsque vous recevrez ce lien il faudra changer l\'addresse du server selon la votre</b></p>');
        $ocrSix->addProjectMedia($media);
        $ocrSix->setIsPublished(true);
        $manager->persist($media);
        $manager->persist($ocrSix);

        $manager->flush();
    }
}
