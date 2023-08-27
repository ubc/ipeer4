### Stop tests on the first failure

All tests are run by default, even if there are failures. To stop running tests
when a test fails, add the `--stop-on-failure` option:

    ./vendor/bin/sail artisan test --stop-on-failure

### Only run certain tests via filter

To run just one test class:

    ./vendor/bin/sail artisan test --filter UserControllerTest

To run just one function within a test class:

    ./vendor/bin/sail artisan test --filter UserControllerTest::test_filter_users

### Only run certain tests via grouping

To run a set of specific tests, use the @group annotation to add them to the same group and then run only that group's tests, e.g.:

    /**
     * @group failing
     */
    public function test_filter_users()
    {
    ...

This adds test_filter_users() to the 'failing' group.

Then we can run the test using either of these two:

    ./vendor/bin/sail artisan test --group failing
    ./vendor/bin/sail phpunit --group failing

### Debug Output

To get the response from a request:

    $resp = $this->getJson($this->url);
    $resp->dump();

Sometimes, you just need to print something out in the test case itself
temporarily for debug reasons. STDOUT is swallowed by phpunit, so you have to
print to STDERR:

    fwrite(STDERR, "\n--- Some Message ---\n");
    fwrite(STDERR, "\n". print_r($someArray, true) ."\n");
