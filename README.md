# qd_getyoutube
 A little modx snippet that cache getYoutube output, so it won't exceed YouTube Api Key limit

How to use this:
1. Create option varaibles:
    qd_getyoutube.assets_url - {assets_path}components/qd_getyoutube
    qd_getyoutube.core_path - {core_path}components/qd_getyoutube/

2. Create and call snippet with this piece of code, to create db model:
    include_once MODX_CORE_PATH . 'components/qd_getyoutube/_build/build.schema.php';

3. Create new snippet, copy and paste into it text from this file: /elements/snippets/snippet.qd_getYoutube.php

4. Call this newly created snippet with parameters that are under comment "qd_getYoutube external variables init"

WARNING:
If this shit doesn't want to work you may need to edit 'modscript.class.php' file:

    Bug Summary:
    Under certain conditions, code in the modScript.class.php file (line 81), the parser is null and causes a fatal PHP error at the second line below:
        $maxIterations= intval($this->xpdo->getOption('parser_max_iterations',null,10));
        $this->xpdo->parser->processElementTags(

    Bug Solution:
        $maxIterations= intval($this->xpdo->getOption('parser_max_iterations',null,10));
        if (!isset($this-xpdo->parser)) {
            $this->xpdo->getParser();
        }
        $this->xpdo->parser->processElementTags(

    More Info: https://github.com/modxcms/revolution/issues/13482