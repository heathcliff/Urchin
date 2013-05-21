Urchin contains the bulk of the business logic and data manipulation functions that make up the core of our Drupal-based publishing platform. Helper classes for parsing nodes, getting lists of nodes and nids, and accessing fields are available, with the overall goal of providing a solid kick-start to any Drupal-based editorial site.

Installation
--

Urchin must be placed in your template's lib directory. Rather than copy the files manually, though, it's best to add Urchin as a git submodule. This allows for easy command-line updating in the future. From the base of your Drupal project, add Urchin as a git submodule. For example, assume your project root is similar to this: 

    /projects/hightimes
    - .git
    - .gitignore
    - drupal
    - frontend
    - index.html

Run the following commands:

    $ cd /projects/hightimes
    $ git submodule add git@github.com:AsheAvenue/Urchin.git drupal/sites/all/themes/hightimes/lib/Urchin
 
Urchin will then be included in your project. To reference it within your project, add the following line to the top of your template.php file, just after the opening PHP tag:

    require_once('lib/Urchin/Urchin.php');
    
Reference
--

Urchin contains the following chainable functions used for querying for articles:

- Base::exclude($exclude)
- Base::field($field_name, $field_value)
- Base::recent()
- Base::limit($limit)
- Base::vocabularyTerm($vocabulary, $term)
- Base::execute()
- Article::get($type)
- Article::category($category)
- Article::series($series)
- Article::getCollection($nid)
- Pager::limit($limit)

Examples:

    $video_articles = Article::get('article_video')
                        ->recent()
                        ->limit(1)
                        ->execute();
                        
    $gallery_articles = Article::get('node_type_article_gallery')
                          ->recent()
                          ->limit(3)
                          ->exclude(Node::getNids(array_merge($video_articles, $news_articles)))
                          ->execute();
                          
    $recent_articles  = Pager::get()
                        ->recent()
                        ->limit(10)
                        ->exclude(Node::getNids($entertainment_articles))
                        ->execute();
                        
                        
