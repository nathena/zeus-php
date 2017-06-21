<?php
namespace test_bus;

class a
{
    public static function test()
    {
        print_r(get_class(new self()));
    }
}

class  b extends a
{

}

b::test();