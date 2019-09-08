import glob, os
os.chdir("./")
for file in glob.glob("*.php"):
    split = file.split(".")
    name = split[0]
    ext  = split[1]

    newname = f"./Repositories/{name}Repository.{ext}"
    print(newname)

    try:
        file = open(newname, 'r')
    except IOError:
        file = open(newname, 'w')
        file.write(f"""<?php
namespace Omnibus\\Models\\Repositories;
use Omnibus\\Core\\Repository;
use Omnibus\\Models\\{name};


class {name}Repository extends Repository
{{
    protected const ENTITY = {name}::class;

}}""")
        file.close()
