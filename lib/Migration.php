<?php

namespace sgkirby\SendMentions;

use Kirby\Data\Data;
use Kirby\Toolkit\F;

class Migration
{
    public function run()
    {
        if (!F::exists(kirby()->root('site') . '/logs/sendmentions/sendmentions.log')) {
            // loop through all pages
            foreach (site()->index('true') as $page) {
                $jsonfile = $page->root() . DS . '.sendmentions.json';
                if (F::exists($jsonfile)) {
                    $yamlfile = $page->root() . DS . '_sendmentions' . DS . 'sendmentions.yml';
                    $data = Data::read($jsonfile, 'json');
                    $newdata = [];

                    // translate old data into new format
                    foreach ($data as $url => $pings) {
                        foreach ($pings as $type => $meta) {
                            if ($type != 'archive.org') {
                                $meta['type'] = $type;
                                $newdata[$url]['mention'] = $meta;
                            } else {
                                $newdata[$url][$type] = $meta;
                            }
                        }
                    }

                    // write new yaml file and delete old json file
                    Data::write($yamlfile, $newdata, 'yml');
                    unlink($jsonfile);
                    SendMentions::logger('>>> old JSON data migrated to YAML', false);
                }
            }
        }
    }
}
