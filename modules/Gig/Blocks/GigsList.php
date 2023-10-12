<?php
namespace Modules\Gig\Blocks;

use Illuminate\Support\Facades\DB;
use Modules\Candidate\Models\Category;
use Modules\Gig\Models\Gig;
use Modules\Gig\Models\GigCategory;
use Modules\Job\Models\Job;
use Modules\Template\Blocks\BaseBlock;

class GigsList extends BaseBlock
{
    function __construct()
    {
        $this->setOptions([
            'settings' => [
                [
                    'id' => 'title',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => __("Title")
                ],
                [
                    'id' => 'sub_title',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => __("Sub Title")
                ],
                [
                    'id'        => 'number',
                    'type'      => 'input',
                    'inputType' => 'number',
                    'label'     => __('Number Items')
                ],
                [
                    'id'           => 'gig_categories',
                    'type'         => 'select2',
                    'label'        => __('Select Gig Categories'),
                    'select2'      => [
                        'ajax'     => [
                            'url'      => route('gig.admin.category.getForSelect2'),
                            'dataType' => 'json'
                        ],
                        'width'    => '100%',
                        'multiple' => "true",
                    ],
                    'pre_selected' => route('gig.admin.category.getForSelect2', ['pre_selected' => 1])
                ],
                [
                    'id'            => 'order',
                    'type'          => 'radios',
                    'label'         => __('Order'),
                    'values'        => [
                        [
                            'value'   => 'id',
                            'name' => __("Date Create")
                        ],
                        [
                            'value'   => 'title',
                            'name' => __("Title")
                        ],
                        [
                            'value'   => 'is_featured',
                            'name' => __("Featured")
                        ],
                    ]
                ],
                [
                    'id'            => 'order_by',
                    'type'          => 'radios',
                    'label'         => __('Order By'),
                    'values'        => [
                        [
                            'value'   => 'asc',
                            'name' => __("ASC")
                        ],
                        [
                            'value'   => 'desc',
                            'name' => __("DESC")
                        ],
                    ]
                ],
                [
                    'id' => 'load_more_url',
                    'type' => 'input',
                    'inputType' => 'text',
                    'label' => __("Load More Url")
                ],
                [
                    'id'      => 'ids',
                    'type'    => 'select2',
                    'label'   => __('Or Filter by Ids'),
                    'select2' => [
                        'ajax'  => [
                            'url'      => route('gig.admin.getForSelect2'),
                            'dataType' => 'json'
                        ],
                        'width' => '100%',
                        'allowClear' => 'true',
                        'placeholder' => __('-- Select --'),
                        'multiple' => "true",
                    ],
                    'pre_selected'=> route('gig.admin.getForSelect2', ['pre_selected' => 1])
                ]
            ],
            'category'=>__("Gig Blocks")
        ]);
    }

    public function getName()
    {
        return __('Gigs List');
    }

    public function content($model = [])
    {
        $model = block_attrs([
            'title' => '',
            'sub_title' => '',
            'gig_categories' => '',
            'number' => 12,
            'order' => 'id',
            'order_by' => 'desc',
            'load_more_url' => '',
            'ids'=> []
        ], $model);

        $model['rows'] = $this->query($model,false);

        return view("Gig::frontend.layouts.blocks.gigs-list.style-1", $model);
    }

    public function contentAPI($model = []){

    }

    public function query($model, $all = true){
        $ids = $model['ids'] ?? [];
        $model_gigs = Gig::with(['location','hasWishList','translations'])->select("bc_gigs.*");
        if(empty($model['order'])) $model['order'] = "id";
        if(empty($model['order_by'])) $model['order_by'] = "desc";
        if(empty($model['number'])) $model['number'] = 6;
        if(!empty($ids) && count($ids) > 0)
        {
            $model_gigs->whereIn('id',$ids);
        }else{
            if ($all == false){
                if (!empty($model['gig_categories']) && is_array($model['gig_categories']) && count($model['gig_categories']) > 0) {
                    $gig_categories = $model['gig_categories'];
                    $model_gigs->where(function ($query) use ($gig_categories){
                        $query->whereIn('cat_id', $gig_categories)
                            ->orWhereIn('cat2_id', $gig_categories)
                            ->orWhereIn('cat3_id', $gig_categories);
                    });
                }
            }
            $model_gigs->orderBy("bc_gigs.".$model['order'], $model['order_by']);
            if($model['order'] == 'is_featured'){
                $model_gigs->orderBy("bc_gigs.id", $model['order_by']);
            }
        }
        $model_gigs->where("bc_gigs.status", "publish");

        $model_gigs->groupBy("bc_gigs.id");

        if(!empty($ids) && count($ids) > 0){
            $imploded_strings = implode("','", $ids);
            return $model_gigs->limit(50)->orderByRaw(DB::raw("FIELD(id, '$imploded_strings')"))->get();
        }else{
            return $model_gigs->limit($model['number'])->get();
        }
    }
}
