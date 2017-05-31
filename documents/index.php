<?php

// <style type="text/css">
//             .centerBox
//             {        
//                 /* Firefox */
//                 display:-moz-box;
//                 -moz-box-orient:vertical;
//                 -moz-box-pack:center;
//                 -moz-box-align:center;

//                 /* Safari and Chrome */
//                 display:-webkit-box;
//                 -webkit-box-orient:vertical;
//                 -webkit-box-pack:center;
//                 -webkit-box-align:center;

//                 /* W3C */
//                 display:box;
//                 box-orient:vertical;
//                 box-pack:center;
//                 box-align:center;
//             }
//         </style>
//         <div style="margin: 5px; width: 250px; border: solid 1px; height: 100px;">
//             <div class="centerBox" style="height: 100px;">Hello World</div>
//         </div>        
//         <div style="margin: 5px; width: 250px; border: solid 1px; height: 100px;">
//             <div class="centerBox" style="height: 100px; width: 250px;">Hello World</div>
//         </div>

// db.tb_contact.update( {}, { $rename: { 'customer_check': 'is_customer', 'employee_check': 'is_employee' } }, false, true );

// MongoDB nằm ở thái cực document. Tập hợp nhiều document gọi là collection. Ví dụ collection access_logs chứa các document có dạng:

// ...
// {user_id: 1, accessed_at: "2010/12/31 08:05:01"} // document
// {user_id: 2, accessed_at: "2010/12/31 08:12:16"} // document
// {user_id: 1, accessed_at: "2010/12/31 08:15:07"} // document
// ...
// Viết dấu {} ý là các document có thể không luôn luôn có cùng là 2 phần tử

// ==================================================================================================================================
// ensure that the voter hasn’t voted yet, and, if not,
// increment the number of votes and
// add the new voter to the array.
// MongoDB’s query and update features allows us to perform all three actions in a single operation. Here’s what that would look like from the shell:

// // Assume that story_id and user_id represent real story and user ids.
// db.stories.update({_id: story_id, voters: {'$ne': user_id}},
//   {'$inc': {votes: 1}, '$push': {voters: user_id}});
// ==================================================================================================================================


error_reporting(E_ALL);
ini_set('display_errors', '1');

$connection = new Mongo();

// $connection = new Mongo( "118.69.201.119:27017" );

$db = $connection->selectDB('nc');

//$db = $connection->dbname;

// $collection = $db->addresses;

$addresses = $db->addresses;

// ================================== ================== FILE IMAGE ============= ==========================================
// $grid = $db->getGridFS();
// // The file's location in the File System
// $path = 'C:\\wamp\\www\\nhachung.vn\\app\\webroot\\mongo\\';
// $filename = '18209.jpg';
// $storedfile = $grid->storeFile(
//     $path . $filename,
//     array( "metadata" => array( "filename" => $filename)),
//     array("filename" => $filename)
//     );
// echo $storedfile;

// ================================== MÔ HÌNH DỮ LIỆU QUAN HỆ TRONG NOSQL - MONGO ==========================================

// // thêm 1 document (dòng) vào bios collection tables
// $ck = array(
//     'first_name' => 'Clark',
//     'last_name'  => 'Kent',
//     'address'    => '344 Clinton St., Apt. #3B',
//     'city'       => 'Metropolis',
//     'state'      => 'IL',
//     'zip'        => '62960',
//     'superpowers' => array( 'superhuman strength', 'invulnerability', 'flight', 'superhuman speed', 'heat vision' )
// );
// // $addresses->save($ck);
// // Defining the map function
// $map = new MongoCode("function() { emit(this.state,1); }");
// // Defining the reduce function
// // EOD sẽ có sao hiển thị vậy, còn EOT sẽ hiểu các biến bên trong http://php.net/manual/en/language.types.string.php
// $reduce = new MongoCode(<<<'EOD'
// function(k, vals) {
//     var sum = 0;
//     for (var i in vals) {
//         sum += vals[i];
//     }
//     return sum;
// }
// EOD
// );
//  // In this simple example, it’s easy to miss that the value returned by the reduce function matches the structure as the document emitted by the map function.
// $mr = $db->command(array(
//     "mapreduce" => "addresses",
//     "map" => $map,
//     "reduce" => $reduce,
//     "out" => array("merge" => "stateCounts") // It has four possible values: "inline" => 1, "replace" => collectionName, "reduce" => collectionName, and "merge" => collection Name.
// ));
//  echo '<pre>';
// print_r( $mr );
// echo '</pre>';
// // Tìm find() trong Collection vừa tạo từ function command ở trên
// $states = $db->selectCollection($mr['result'])->find(); // Because we used one of the methods that creates a collection, we need to perform a find on the collection, then iterate over the cursor find returns.
// foreach ($states as $state) {
//     echo '<pre>';
//     print_r( $state );
//     echo '</pre>';
//     echo $state['value']." heros live in ". $state['_id'] . "<br>";
// }

// print_r( $addresses->distinct('address') ); die;
// echo $addresses->count();

// --------------------------------------------------------------- group by ----------------------------
// $db->animals->drop();
// $db->animals->save(array("class" => 'mammal', 'name' => 'kangaroo'));
// $db->animals->save(array("class" => 'mammal', 'name' => 'seal'));
// $db->animals->save(array("class" => 'mammal', 'name' => 'dog'));
// $db->animals->save(array("class" => 'bird', 'name' => 'eagle'));
// $db->animals->save(array("class" => 'bird', 'name' => 'ostrich'));
// $db->animals->save(array("class" => 'bird', 'name' => 'emu'));
// $db->animals->save(array("class" => 'reptile', 'name' => 'snake'));
// $db->animals->save(array("class" => 'reptile', 'name' => 'turtle'));
// $db->animals->save(array("class" => 'amphibian', 'name' => 'frog'));
// $reduce = new MongoCode(<<<'EOF'
//     function(doc,counter) {
//         counter.items.push(obj.name);
//     }
//     EOF
// );
// $g = $db->animals->group(
//     array('class' => 1),
//     array('items' => array()),
//     $reduce
// );
// echo json_encode($g['retval'], JSON_PRETTY_PRINT);
// --------------------------------------------------------------- Đếm theo group by ----------------------------
// $reduce = new MongoCode('function(doc,counter) {
//     counter.count++;
// }');
// $g = $db->animals->group(
//     array('class' => 1),
//     array('count' => 0),
//     $reduce
// );
// echo json_encode($g['retval'], JSON_PRETTY_PRINT);

// Liệt kê tất cả tags trong table post chẳng hạn, ở đây là liệt kê tất cả superpowers trong bios
// echo '<pre>';
// print_r($db->command(
//     array(
//         "distinct" => "bios",
//         "key" => "superpowers"
//     )
// ));
// echo '</pre>';

// $addresses->save(array("created" => new MongoDate()));
// $birth = new MongoDate(strtotime("1953-04-13 00:00:00"));
// echo '<pre>';
// print_r( $birth );
// echo '</pre>';
// echo '<pre>';
// print_r( date('Y-M-d h:m:s', $birth->sec) );
// echo '</pre>';

// $ts=new MongoId('51547a02bb07f8d96f000723');
// $added_epoch=$ts->getTimestamp();
// it shows 1364490754

// $id1 = new MongoId();
// $id2 = new MongoId();
// $post = array(
//     '_id'     => $id1,
//     'title'   => 'MongoDB and PHP',
//     'text'    => 'MongoDB an PHP are like PB and J. Good alone, great together.',
//     'related' => array($db->createDBRef('articles', $id2) )
//     );
// $post2 = array(
//     '_id'     => $id2,
//     'title'   => 'MongoDB, PHP and You',
//     'text'    => 'Before MongoDB I felt so empty using PHP. Now I have a new lease on life',
//     'related' => array($db->createDBRef('articles', $id1) )
//     );
// $db->articles->insert($post);
// $db->articles->insert($post2);
// foreach( $db->articles->find() as $p) {
// 	echo '<pre>';
//     print_r($p);
//     echo '</pre>';
// }
// echo '<pre>';
// print_r($db->getDBRef($post2['related'][0]));
// echo '</pre>';

// $id = new MongoId();
// $post = array(
//     'title' => 'MongoDB and PHP',
//     'text' => 'MongoDB an PHP are like PB and J. Good alone, great together',
//     'author' => 'spf13'
// );
// $post2 = array(
//     '_id' => $id,
//     'title' => 'MongoDB, PHP and You',
//     'text' => 'Before MongoDB I felt so empty using PHP. Now I have a new lease on life',
//     'author' => 'spf13'
// );
// $user = array(
// 	'_id' => 'spf13',
//     'name' => 'Steve Francia'
// );
// $db->articles->insert($post, array('safe' => true));
// $db->articles->insert($post2);
// $db->users->insert($user);
// $results = $db->articles->find(array('author' => $user['_id']));
// foreach ( $results as $p) {
// 	   echo '<pre>';
//     print_r($p);
//     echo '</pre>';
// }











// ================================ DANH SÁCH CÁC VÍ DỤ CỤ THỂ VỀ CÁC LOẠI TRUY VẤN ========================================

// non-blocking in background
// $db->numbers->ensureindex(array( 'name' => 1, 'background' => true ));

// unique
// $db->numbers->ensureindex(array( 'email' => 1, 'unique' => true ));

// $blogpost->ensureIndex(array(
//     "ts" => -1,                // index cho cột thời gian desc, 1 là ascending, -1 là descending. Using descending order is useful in a few cases. One in particular is dates when typically accessing the most recent data.
//     "comments.author" => 1
// ));

// $db->numbers->ensureindex(array( 'num' => 1 ));
// echo '<pre>';
// print_r( $db->numbers->find(
//                 array('num' => array( '$gt' => 50000, '$lt' => 50002))
//             )->explain()
// );
// echo '</pre>';

// $results = $addresses->find(
//     array( 'state' =>
//         array('$in' =>
//             array('NY', 'CA') // IN trong SQL
//         )
//     )
// );


// $results = $addresses->find(
//     array( 'superpowers' =>
//         array('$in' =>
//             array('flight', 'agility')
//         )
//     )
// );

// $results = $addresses->find(
//     array( 'superpowers' =>
//         array('$nin' =>
//             array('wall crawling', 'agility') // NOT IN trong SQL
//         )
//     )
// );

// $results = $addresses->find(
//     array( 'superpowers' =>
//         array('$all' =>
//             array('agility', 'wall crawling') // IN va AND
//         )
//     )
// );

// $results = $addresses->find(
//     array( 'superpowers' =>
//          array (
//              'agility',
//              'stamina',
//              'spidey sense',
//              'web shooters',
//              'superhuman strength',
//              'superhuman intelligence', // giong IN va AND, nhung so sanh chinh xac mang chu k phai value voi value, nen phai dung ca thu tu cac vi tri trong mang
//              'wall crawling',
//              'really really good looking'
//          )
//     )
// );

// $results = $addresses->find(array(),array('superpowers' => array('$slice' => 2))); // Lấy ra tất cả nhưng với những key có thuộc tính superpowers thì chỉ lấy 2 giá trị

// $results = $addresses->find(array(),array('superpowers' => array('$slice' => array(2, 3)))); // giống ở trên nhưng lấy từ vị trí key 2 (tính từ 0) và 3 cái tiếp theo

// echo '<pre>';
// print_r($addresses->findone(
//     array( 'first_name' => 'Peter', 'last_name' => 'Parker'), // lấy theo đk và chỉ lấy 1 fields duy nhất đó là superpowers khi dùng '_id' => 1
//     array('_id' => 1, 'superpowers' => array('$slice' => 2))
// ));
// echo '</pre>';

// This example will return any document that has five elements in the superpowers array,
// which in our data set would result in the Clark Kent document.
// $results = $addresses->find(
//     array( 'superpowers' =>
//         array('$size' => 3)
//     )
// );

// $tengen = array(
//     'name' => '10gen',
//     'locations' => array(
//         array(
//             'street no' => '100',
//             'street'    => 'Marine Parkway',
//             'suite'     => '175',
//             'city'      => 'Redwood City',
//             'state'     => 'CA',
//             'zip'       => '94065'
//         ),
//         array(
//             'street no' => '134',
//             'street'    => '5th Avenue',
//             'floor'     => '3rd',
//             'city'      => 'New York',
//             'state'     => 'NY',
//             'zip'       => '10011'
//         ),
// ));
// $db->company->save($tengen);
// $results = $db->company->find( array(
// 	'locations' => array( 
// 		'$elemMatch' => array( 
// 			'state' => 'NY',
// 			'city' => 'New York', // dùng cho trường hợp các phần phụ locations là những mảng, và đk cho từng mảng
// 		) 
// 	)
// ));

// $results = $db->company->find( array(
// 	'locations.zip' => array(
// 		'$in' => array(
// 			'10011',
// 			'10012'
// 		)
// 	)
// ));

// $results = $db->company->find( array(
// 	'locations.zip' => '10011'
// ));

// The following example can be read as “Find me all records that have either the state
// NY or the city New York and either the first name Eliot or the last name Parker.”
// In our data set, it would result in both documents with NY as a state:
// $results = $addresses->find(
//         array( '$and' => array(
//             array('$or' => array(
//                 array('state' => 'NY'),
//                 array('city' => 'New York')
//                 )
//             ),
//             array('$or' => array(
//                 array('first_name' => 'Eliot'),
//                 array('last_name' => 'Parker')
//                 )
//             )
//         ))
//     );

// foreach ($results as $key => $document){

// 	echo '<pre>';
// 	print_r($document);
// 	echo '</pre>';

// }

// Updating Multiple Records
// By default the update (or save) methods only work on a single document. Both have an
// additional parameter that accepts an array of options. To update all documents that
// match  the criteria provided,  simply pass  in array("multiple" => true)  in  the  third
// parameter.

// Deleting Multiple Records
// remove provides the same “options” parameter; however, by default, remove operates
// on multiple records, so no additional action is needed. If you want to limit it to one
// document, the justOne option set to true will do the trick.

// $address = array(    'first_name' => 'Peter',    'last_name'  => 'Parker',    'address'    => '175 Fifth Ave',    'city'       => 'New York',    'state'      => 'NY',    'zip'        => '10010'    );

//$addresses->insert($address);/ tam thoi khong insert them

//$addresses->insert($address, array('safe' => true));

// The insert method itself will add the about-to-be-created _id to the array (or object)passed in. This behavior is important to understand and likely represents a change fromwhat you are likely used to. It does this before sending the data over to the database.The insert method does not return the primary key; rather, it sets it on the array orobject provided. To access the primary key, simply reference it:

// $pk = $address['_id'];

//Like a key value store, you can access the document by the primary key:$id = new MongoId('4ba667b0a90578631c9caea1');$pp = $addresses->findone( array( '_id' => $id ) );Unlike a key value store, you can access the document by any other key:$pp = $addresses->findone( array(    'first_name' => 'Peter',    'last_name' => 'Parker'    ) );

// $addresses->update(    array( '_id' => new MongoId('51a6f444a86c495625c43df0')),    array( '$set' => array( 'zip' => '123' ) ));

//$addresses->update(    array( 'first_name' => 'Peter', 'last_name' => 'Parker'),    array( '$set' =>        array( 'superpowers' =>            array( 'agility', 'stamina', 'spidey sense', 'web shooters',                   'super human strength', 'super human intelligence' )        )    ));

//$addresses->update(    array( 'first_name' => 'Peter', 'last_name' => 'Parker'),    array( '$push' => array( 'superpowers' => 'wall crawling' )));

//class Hero {}$hero = new Hero();$hero->first_name = 'Eliot';$hero->last_name = 'Horowitz';$hero->address = '134 Fifth Ave';$hero->city = 'New York';$hero->state = 'NY';$hero->zip = '10010';$hero->superpowers = array( 'agility', 'super human intelligence', 'wall crawling' );$addresses->save($hero);

//$criteria = array('_id'=> new MongoId('51a6f444a86c495625c43df0'));$addresses->remove($criteria, array("justOne" => true) );


// $id = new MongoId('51a7067fa86c495b25e104c6');
// $pp = $addresses->findone( array( '_id' => $id ) );


// $id = $pp['_id'];

// echo $id->getTimestamp();

// echo '<pre>';
// print_r($pp);
// echo '</pre>';

//$results = $db->numbers->find()->limit(2);
//foreach ($results as $document){

//echo '<pre>';
//    print_r($document);
//echo '</pre>';

//}

// dung ' nhay de '$lt' la 1 chuoi chu khong phai 1 bien trong php

//$results = $db->numbers->find( array( 'num' => array( '$lt' => 15 )))->limit(3)->skip(20)->sort(array('num'=> -1));

//These include $gt, $lt,$gte, and $lte, which stand for greater than, less than, greater than or equal, and lessthan or equal.
//$results = $db->numbers->find( array( 'num' => array( '$lt' => 15 )));
//foreach ($results as $document){  
//echo '<pre>';
//  print_r($document);
//echo '</pre>';

//}


// $results = $addresses->find(        array( 'superpowers' => 'agility')    );
// foreach ($results as $document){
// echo '<pre>';
//   print_r($document);
// echo '</pre>';

// }








// neu muon lay mot so fields tra ve thoi thi dung bo vao array thu 2
//$pw = $db->users->findOne(array('username' => 'spf13'), array('password'));

//echo '<pre>';
//print($pw);
//echo '</pre>';i

//So far, as we only have a single document in our database, we will use the shell toquickly create 250,000 documents. Just create a PHP file with the following code andrun it:

//$conn = new Mongo();$db = $conn->selectDB('test');$db->numbers->drop();for ($i = 0; $i < 250000; $i++) {    $db->numbers->save(array('num' => $i));}You could also do this in the shell with the following:use test;db.numbers.drop();for(i=0; i < 250000; i++) {    db.numbers.save({num: i});}






echo 'ok';

die;