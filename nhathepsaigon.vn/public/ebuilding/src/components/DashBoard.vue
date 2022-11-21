<template>
  <div>
      <div class="row">
        <div class="col-md-12">
          <div class="x_panel">
            <div class="x_content">
              <div class="row">
                <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
                  <a href="">
                    <div class="well profile_view">
                      <div class="col-sm-12">

                        <div class="left col-xs-7">
                          <h2>Block C Khu dân cư hạnh phúc bản nháp</h2>
                          <ul class="list-unstyled building-ul">
                            <li><i class="fa fa-building"></i> công ty cp dvkt giải pháp an gia</li>
                            <li><i class="fa fa-map-marker"></i> Nguyễn Văn Linh</li>
                          </ul>
                        </div>
                        <div class="right col-xs-5 text-center">
                          <img src="/designer/images/img.jpg" alt="" class="img-circle img-responsive">
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
                  <a href="">
                    <div class="well profile_view">
                      <div class="col-sm-12">

                        <div class="left col-xs-7">
                          <h2>Block C Khu dân cư hạnh phúc bản nháp</h2>
                          <ul class="list-unstyled building-ul">
                            <li><i class="fa fa-building"></i> công ty cp dvkt giải pháp an gia</li>
                            <li><i class="fa fa-map-marker"></i> Nguyễn Văn Linh</li>
                          </ul>
                        </div>
                        <div class="right col-xs-5 text-center">
                          <img src="/designer/images/img.jpg" alt="" class="img-circle img-responsive">
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
                  <a href="">
                    <div class="well profile_view">
                      <div class="col-sm-12">

                        <div class="left col-xs-7">
                          <h2>Block C Khu dân cư hạnh phúc bản nháp</h2>
                          <ul class="list-unstyled building-ul">
                            <li><i class="fa fa-building"></i> công ty cp dvkt giải pháp an gia</li>
                            <li><i class="fa fa-map-marker"></i> Nguyễn Văn Linh</li>
                          </ul>
                        </div>
                        <div class="right col-xs-5 text-center">
                          <img src="/designer/images/img.jpg" alt="" class="img-circle img-responsive">
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
                  <a href="">
                    <div class="well profile_view">
                      <div class="col-sm-12">

                        <div class="left col-xs-7">
                          <h2>Block C Khu dân cư hạnh phúc bản nháp</h2>
                          <ul class="list-unstyled building-ul">
                            <li><i class="fa fa-building"></i> công ty cp dvkt giải pháp an gia</li>
                            <li><i class="fa fa-map-marker"></i> Nguyễn Văn Linh</li>
                          </ul>
                        </div>
                        <div class="right col-xs-5 text-center">
                          <img src="/designer/images/img.jpg" alt="" class="img-circle img-responsive">
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
                  <a href="">
                    <div class="well profile_view">
                      <div class="col-sm-12">

                        <div class="left col-xs-7">
                          <h2>Block C Khu dân cư hạnh phúc bản nháp</h2>
                          <ul class="list-unstyled building-ul">
                            <li><i class="fa fa-building"></i> công ty cp dvkt giải pháp an gia</li>
                            <li><i class="fa fa-map-marker"></i> Nguyễn Văn Linh</li>
                          </ul>
                        </div>
                        <div class="right col-xs-5 text-center">
                          <img src="/designer/images/img.jpg" alt="" class="img-circle img-responsive">
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
                  <a href="">
                    <div class="well profile_view">
                      <div class="col-sm-12">

                        <div class="left col-xs-7">
                          <h2>Block C Khu dân cư hạnh phúc bản nháp</h2>
                          <ul class="list-unstyled building-ul">
                            <li><i class="fa fa-building"></i> công ty cp dvkt giải pháp an gia</li>
                            <li><i class="fa fa-map-marker"></i> Nguyễn Văn Linh</li>
                          </ul>
                        </div>
                        <div class="right col-xs-5 text-center">
                          <img src="/designer/images/img.jpg" alt="" class="img-circle img-responsive">
                        </div>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="clearfix"></div>
                <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                  <ul class="pagination pagination-split">
                    <li><a href="#asd">1</a></li>
                    <li><a href="#">2</a></li>
                    <li><a href="#">3</a></li>
                    <li><a href="#">4</a></li>
                    <li><a href="#">5</a></li>
                    <li>...</li>
                    <li><a href="#">6</a></li>
                    <li><a href="#">7</a></li>
                    <li><a href="#">8</a></li>
                    <li><a href="#">9</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
  </div>
</template>

<script>
  export default {
    name: 'DashBoard',
    components: {
    },
    mounted() {

    },
    methods: {
      toggleBodyClass(addRemoveClass, className) {
        const el = document.body
        if (addRemoveClass === 'addClass') {
          el.classList.add(className)
        } else {
          el.classList.remove(className)
        }
      }
    },
    created () {
      if(this.$route.query.ft){
        this.toggleBodyClass('removeClass', 'login')
        this.toggleBodyClass('addClass', 'nav-md')
      }
    }
  }
</script>

<style scoped>

</style>
