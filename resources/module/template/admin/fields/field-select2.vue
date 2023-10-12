<template>
    <select>
    </select>
</template>
<script>

import { abstractField } from "vue-form-generator";

export default {
    mixins: [ abstractField ],
    data(){
        return {
            options:[],
            selectedText:''
        }
    },
    mounted: function () {
        var vm = this;

        $(vm.$el).select2(vm.schema.select2);
        if(this.schema.pre_selected && this.value){
            $.ajax({
                method:'get',
                url:this.schema.pre_selected,
                data:{
                    selected:this.value
                },
                dataType:'json',
                success:function (json) {
                    if(vm.schema.select2.multiple){
                        if(typeof json.items !='undefined' && typeof json.items == 'object' && json.items.length){
                            for(var i = 0 ; i < json.items.length; i++){
                                var newOption = new Option(json.items[i].text, json.items[i].id, true, true);
                                $(vm.$el).append(newOption);
                            }
                            $(vm.$el).select2('val',this.value)
                            $(vm.$el).trigger('change');
                        }
                        return;
                    }else{
                        var newOption = new Option(json.text, vm.value, false, false);
                        $(vm.$el).append(newOption).trigger('change');
                    }
                    //vm.selectedText = json.text;
                }

            })
        }else{

        }
        $(vm.$el).on('change',function () {
            // vm.$emit('input', $(this).val());
            vm.value = $(this).val();
        });
    },
    methods:{
        onChange(value) {
            var vm = this;
            $(vm.$el).select2(vm.schema.select2);
            if(this.schema.pre_selected && value){
                $.ajax({
                    method:'get',
                    url:this.schema.pre_selected,
                    data:{
                        selected:vm.value
                    },
                    dataType:'json',
                    success:function (json) {
                        if(vm.schema.select2.multiple){
                            if(typeof json.items !='undefined' && typeof json.items == 'object' && json.items.length){
                                $(vm.$el).empty();
                                for(var i = 0 ; i < json.items.length; i++){
                                    var newOption = new Option(json.items[i].text, json.items[i].id, true, true);
                                    if ($(vm.$el).find('option[value="'+json.items[i].id+'"]').length === 0){
                                        $(vm.$el).append(newOption);
                                    }
                                }
                                $(this.$el).val(value).trigger('change');
                            }else{
                                $(vm.$el).empty().trigger('change');
                            }
                            return;
                        }
                    }
                })
            }else{
                $(vm.$el).empty().trigger('change');
            }
        }
    },
    watch: {
        value(value) {
            if ([...value].sort().join(",") !== [...$(this.$el).val()].sort().join(","))
            {
                console.log("watch change", value);
                this.onChange(value);
            }

        },
        // value: function (value) {
        //     // update value
        //     $(this.$el)
        //         .val(value)
        //         .trigger('change')
        // },
        options: function (options) {
            // update options
            //$(this.$el).empty().select2({ data: options })
        }
    },
    destroyed: function () {
        $(this.$el).off().select2('destroy')
    }
};
</script>
