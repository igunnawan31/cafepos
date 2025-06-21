@echo off
for %%G in (DUPL-06-01 DUPL-06-02 DUPL-06-03 DUPL-06-04 DUPL-06-05 DUPL-06-06 DUPL-06-07 DUPL-06-08) do (
    echo ==============================
    echo Running group: %%G
    echo ==============================
    php vendor\phpunit\phpunit\phpunit tests\OrderAddProductTest.php --group=%%G --testdox --colors=always
)
