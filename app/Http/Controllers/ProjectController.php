<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Auth;
use DB;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
     //   $projects = Project::get();
     //   return view('projects', ['projects' => $projects]);
        $projects = Project::get();
        return view('projects', ['projects' => $projects]);

    }

    public function showall()
    {
        //     return response()->json([

        //     'Projects' => Project::all()

        //  ], Response::HTTP_OK);

        $userData = Project::get();
        return json_encode(array('data' => $userData));
    }

    public function UserDetails()
    {

        //   $test =    DB::select('SELECT users.id,users.name,users.email FROM projects INNER JOIN users ON projects.user_id = users.id');

        $userJoin = DB::table('projects')
            ->select('projects.name', 'users.email')
            ->where('projects.name', 'Plastic')
            ->join('users', 'users.id', '=', 'projects.user_id')
            ->get();
        // return json_encode($userJoin);

        // Get all info
        $usersAll = DB::table('users')->get();
        // return $usersAll;

        // Get single infomation from multiple row
        $users = DB::table('users')->get();
        foreach ($users as $user) { // you can use this same forloop in blade also
            //   echo $user->name;
        }

        //first method is used to get row information
        $user = DB::table('users')->where('name', 'saeed')->first();
        //  return $user->email;

        // get value of the column directly
        $email = DB::table('users')->where('name', 'saeed')->value('email');
        //  return $email;

        // retrieving single row by id
        $user = DB::table('users')->find(1);
        // return $user;

        // Pluck may find emails of all users
        $email = DB::table('users')->pluck('email');

        foreach ($email as $mail) {
            //   echo $mail;
        }

        // get and email both of all users // equal to where query
        $email = DB::table('users')->pluck('email', 'name');

        foreach ($email as $name => $mail) {
            //  echo $mail;
        }

        DB::table('users')->orderBy('id')->chunk(1, function ($users) {
            foreach ($users as $user) {
                //       var_dump($user);
            }
        });

        // count the users
        $users = DB::table('users')->count();
        //   return $users;

        //find the maximum id
        $max_id = DB::table('users')->max('id');
        //  return $max_id;

        if (DB::table('users')->where('name', "saeed")->exists()) {
            //   return "found";
        } else {
            // return "Not found";
        }

        // to avoid duplicaion
        $users = DB::table('users')->distinct()->get();
        // return $users;

        $query = DB::table('users')->select('name', 'email')->get();
        //  $users = $query->addSelect('email')->get();
        // return $query;

        $usersCount = DB::table('users')
            ->select(DB::raw('count(*) as user_count, name'))
            ->where('name', '<>', 1)
            ->groupBy('name')
            ->get();

        //   return $usersCount;

        // order by lastest and old date
        $user = DB::table('users')
            ->latest()
            ->first();

        // in sql query
        $usersRaw = DB::table('select *
    from users
    where exists
    (select 1 from projects
    where projects.user_id = users.id');

        // unlike inner join, this query get all data.
        $usersRaw = DB::table('users')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('projects')
                    ->whereColumn('projects.user_id', 'users.id');
            })
            ->get();

        return $usersRaw;

    }

    public function search(Request $request)
    {
        $userData = Project::where('name', 'LIKE', '%' . $request->name . '%')->get();
        return json_encode(array('data' => $userData));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        /// $input = $request->all();

        if ($image = $request->file('image')) {
            $destinationPath = 'image/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);

        }

        //  Product::create($input);

        $project = new Project();
        $project->name = $request->name;
        $project->description = $request->description;
        $project->image = $profileImage;
        $project->user_id = Auth::user()->id;
        $project->save();

        return redirect()->route('projects.index')
            ->with('success', 'Project saved successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $project = Project::find($id);

        $userData = Project::find($id);
        return json_encode(array('singleInfo' => $userData));

        // return view('projects', ['project' => $project]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $project = Project::find($id);

        return view('projects.edit', ['project' => $project]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
      

        request()->validate([
            'name' => 'required|max:255',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
         ]);

        if ($image = $request->file('image')) {
            $destinationPath = 'image/';
            $profileImage = date('YmdHis') . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);

        }

        $project = Project::find($id);
        $project->name = $request->name;
        $project->description = $request->description;
        $project->image = $profileImage;
        $project->user_id = Auth::user()->id;
        $project->save();

        return redirect()->route('projects.index')
            ->with('success', 'Project updated successfully!');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Project::destroy($id);
        //   return redirect()->route('projects.index')
        //      ->with('success','Project deleted successfully!');
    }
}
