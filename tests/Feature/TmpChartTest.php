<?php
namespace Tests\Feature;
use Tests\TestCase;
class TmpChartTest extends TestCase
{
    public function test_dump(): void
    {
        $this->withSession(['is_admin' => true]);
        file_put_contents(base_path('tmp_dash.html'), $this->get('/admin')->content());
        $this->assertTrue(true);
    }
}
