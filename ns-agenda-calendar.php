<?php

class Calendar {  
     
	public $param;
    /**
     * Constructor
     */
    public function __construct($param, $weekly = false){     //$param is used to set the navigation html path in both personal and avaiable calendar cases
															  //$weekly is used to create a weekly calendar 
		$this->is_avaiable_template = $param;
		if($param)
			 $this->naviHref = htmlentities(get_site_url().'/agenda/');
		else{
			$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
			$myaccount_page_url = '';
			if ( $myaccount_page_id ) {
				$myaccount_page_url = get_permalink( $myaccount_page_id );
			}
			$this->naviHref = htmlentities($myaccount_page_url.'/my-agenda/');
		}
		$this->weekly = $weekly;
		$this-> color_array = array();
    }
     
    /********************* PROPERTY ********************/  
    private $dayLabels = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
     
    private $currentYear=0;
     
    private $currentMonth=0;
     
    private $currentDay=0;
     
    private $currentDate=null;
     
    private $daysInMonth=0;
     
    private $naviHref= null;
     
    /********************* PUBLIC **********************/  
        
    /**
    * print out the calendar
    */
    public function show($is_personal_calendar) {
      
		if (isset($_GET['ns_year'])) {

			$ns_year = $_GET['ns_year'];

		} else {

			$ns_year = date("Y", time());

		}

		if (isset($_GET['month'])) {

			$month = $_GET['month'];

		} else {

			$month = date("m", time());

		}                  
         
        $this->currentYear=$ns_year;
         
        $this->currentMonth=$month;
         
        $this->daysInMonth=$this->_daysInMonth($month,$ns_year);  
        
		$content = '';
		if($is_personal_calendar)
			$content='<div id="calendar" style="width: 100%;">';
		else
			$content='<div id="calendar">';
		
		$content.= 
                        '<div class="box">'.
                        $this->_createNavi().
                        '</div>'.
                        '<div class="box-content">';
							if(!$this->weekly)
								$content.='<ul class="label">'.$this->_createLabels().'</ul>';   
							
                               
							$content.='<div class="clear"></div>';     
							$content.='<ul class="dates">';    
							 
							$weeksInMonth = $this->_weeksInMonth($month,$ns_year);
							// Create weeks in a month
							for( $i=0; $i<$weeksInMonth; $i++ ){
								//case : weekly calendar
								if($this->weekly){
									$content.= $this->_showWeek_avaiability($i);
								}
								else{
									//Create days in a week
									for($j=1;$j<=7;$j++){
										//case: avaiability calendar
										if($this->is_avaiable_template)
											$content.=$this->_showDay_avaiability($i*7+$j);
										//case: personal calendar
										else
											$content.=$this->_showDay($i*7+$j);
									}
								}

							}
                               
						   
						   $content.='</ul>';
							 
							$content.='<div class="clear"></div>';     
		 
					$content.='</div>';
			 
        $content.='</div>';
        return $content;   
    }
     
    /********************* PRIVATE **********************/ 
    /**
    * create the li element for ul
    */
    private function _showDay($cellNumber){
         
        if($this->currentDay==0){
             
            $firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));
                     
            if(intval($cellNumber) == intval($firstDayOfTheWeek)){
                 
                $this->currentDay=1;
                 
            }
        }
         
        if( ($this->currentDay!=0)&&($this->currentDay<=$this->daysInMonth) ){
             
            $this->currentDate = date('Y-m-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));
             
            $cellContent = $this->currentDay;
             
            $this->currentDay++;   
             
        }else{
             
            $this->currentDate =null;
 
            $cellContent=null;
        }
           
		$current_user = wp_get_current_user();   
		$args = array(
			'author'        =>  $current_user->ID,
			'orderby'       =>  'post_date',
			'order'         =>  'ASC',
			'post_type'		=>	'ns_agenda',
			'posts_per_page' => -1
		);
        
		$booking_posts = get_posts( $args );	//all booking posts of current user
		//echo '<pre>'; print_r(get_post_meta(2023, 'booking_info', true)); echo '</pre>';
		
		$booking_event_in = null;
		$booking_event_out = null;
		$cont = 0;
		$exceed = false;
		$exceeded_more_number = '';
		$index_color = 0;
		$color = array('ADD8D8','B7C3DF','BDECBD','FFEECC','FFE3CC','D8C0AD','AD8B9E','BCCAA1','8AAD8A','B77E7E', 'B7987E');
		foreach($booking_posts as $post){
			if($cont > 2)
				$exceed = true;
			if(!$exceed){
				$date_in = get_post_meta($post->ID, 'date_in', true);
				$date_out = get_post_meta($post->ID, 'date_out', true);
				$hour_in = get_post_meta($post->ID, 'hour_in', true);
				$hour_out = get_post_meta($post->ID, 'hour_out', true);
				//get color from palette array
				if($index_color == 11)
					$index_color = 0;
				$col = $color[$index_color];
	
				//formatting to date
				$link = get_post_permalink($post->ID);
				if(!isset($this->color_array[$link])){
					$this->color_array[$link] = $col;
				}
				
				if($date_in == date('d-m-Y', strtotime($this->currentDate))){
					$booking_event_in =  $booking_event_in.' <div class="ns-booking-event-div" style="background-color: #'.$this->color_array[$link].'"><a href="'.$link.'"> '.$hour_in.' - '.$hour_out.'</a></div>';
					$cont++;
				}
				/*if($date_out == date('d-m-Y', strtotime($this->currentDate))){
					$booking_event_out = $booking_event_out.' <div class="ns-booking-event-div" style="background-color: #'.$this->color_array[$link].'"><a href="'.$link.'">END BOOKING '.$post->post_title.'</a></div>';			
					$cont++;
				}	*/	
			}
			else{
				if($date_in == date('d-m-Y', strtotime($this->currentDate)))
					$cont++;
				if($date_out == date('d-m-Y', strtotime($this->currentDate)))
					$cont++;
			}
			$index_color++;
		}
		if($exceed){
			$exceeded_more_number .= ' <div class="ns-booking-event-avaiability-div"> And '.($cont - 3).' more...</div>';
		}
		
		$personal_calendar_modal = '';
		if($cont > 0){
			$personal_calendar_modal = 'ns-open-personal-calendar-modal';
		}
		
        return '<li id="li-'.$this->currentDate.'" class="'.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).
                ($cellContent==null?'mask': $personal_calendar_modal).'"><div class="ns-li-inner-container"><div class="ns-day-number">'.$cellContent.'</div>'.$booking_event_in.' '.$booking_event_out.' '.$exceeded_more_number.'</div><input class="booking-date" value="'.$this->currentDate.'" type="hidden"></li>';
    }
    
	private function rand_color() {
		return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
	}
	
    private function _showDay_avaiability($cellNumber){
         
        if($this->currentDay==0){
             
            $firstDayOfTheWeek = date('N',strtotime($this->currentYear.'-'.$this->currentMonth.'-01'));
                     
            if(intval($cellNumber) == intval($firstDayOfTheWeek)){
                 
                $this->currentDay=1;
                 
            }
        }
         
        if( ($this->currentDay!=0)&&($this->currentDay<=$this->daysInMonth) ){
             
            $this->currentDate = date('Y-m-d',strtotime($this->currentYear.'-'.$this->currentMonth.'-'.($this->currentDay)));
             
            $cellContent = $this->currentDay;
             
            $this->currentDay++;   
             
        }else{
             
            $this->currentDate =null;
 
            $cellContent=null;
        }
		$is_class = false;
		$ava = null;
		$j = 0;		//keeps the count of avaiable quantity for that product
		$i = 0;		//keeps the count of product with 0 avaiability
		//$z = false;
		$at_least_one_disp = false;
		if($this->currentDate != null ){

			$current_day = date('d-m-Y',strtotime($this->currentDate));  
			$arr_availability = get_option('ns_agenda_option_struct_'.$this->currentYear);
			$cont = 0; //this one is used to keep track of the 'and more...' text
			$exceed = false;
			
			if(isset($arr_availability[$current_day])){ 
				foreach ($arr_availability[$current_day] as $hour => $h) { //Cycling on each hour of day
					if($h['available']){	//check if this hour is available
					   
						foreach ($h as $key => $value) { //Cycling on each product of that hour
							if($key != 'available'){ // not consider the key available
								if($value == 0 || $value == null ){		//if not available														
									$i++;	
									$cont++;
									$is_class = true;		
								}
							
								else{ 
									$j++;
									$at_least_one_disp = true; //there's at least one bookable product avaiable
								}
							}
							/*else
								$z = true;*/
						}
					}
				}
			}					
			
			
			
		}

		//$is_hourly = true; //get_option('ns_booking_is_hourly');
		$ns_calendar_empty_cell_hourly = '';
		//if($is_hourly){
			if($at_least_one_disp){		//Check if exist at least one '_bookable' product avaiable to show green cell class
				$ns_calendar_empty_cell_hourly = ' ns-calendar-empty-cell-hourly';
			}
			else{
				//No product available so show red class cell
				if($this->currentDate != ''){
					$ns_calendar_empty_cell_hourly = ' ns-calendar-empty-cell';
				}
				
			}

		//}
		
		if($i == 0 && $j>0) { //all available
			 return '<li id="li-'.$this->currentDate.'" class="'.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).
			($cellContent==null?'mask':'').$ns_calendar_empty_cell_hourly.'"><div class="ns-li-inner-container"><div class="ns-day-number">'.$cellContent.'</div>'.$ava.'</div></li>';
		}
		$cla = '';
		if($i>0 && $j> 0){ //some product available
		
			if($is_class){
				$cla = ' ns-open-modal';
				$ns_calendar_empty_cell_hourly = ' ns-calendar-some-available-cell';
			}
		}		
		
		return '<li id="li-'.$this->currentDate.'" class="'.($cellNumber%7==1?' start ':($cellNumber%7==0?' end ':' ')).
				($cellContent==null?'mask': $cla).$ns_calendar_empty_cell_hourly.'"><div class="ns-li-inner-container"><div class="ns-day-number">'.$cellContent.'</div>'.$ava.'</div><input class="booking-date" value="'.$this->currentDate.'" type="hidden"></li>';
    } 
	 
	 
	private function _showWeek_avaiability($week){
		$days_nav_bar = '<div class="ns-dates-inner-container"><div class="ns-weekly-labels">'.$this->_createWeeklyLabels().'</div>';
		$to_return = '';
		
		return $days_nav_bar.'<li id="li-'.$week.'"'.'class = "ns-calendar-weekly-cell"><div class="ns-week-number">'.$week.'</div></li></div>';
	}
	 
    /**
    * create navigation
    */
    private function _createNavi(){
         
        $nextMonth = $this->currentMonth==12?1:intval($this->currentMonth)+1;
         
        $nextYear = $this->currentMonth==12?intval($this->currentYear)+1:$this->currentYear;
         
        $preMonth = $this->currentMonth==1?12:intval($this->currentMonth)-1;
         
        $preYear = $this->currentMonth==1?intval($this->currentYear)-1:$this->currentYear;
        return
            '<div class="header">'.
                '<a class="ns-agenda-prev" href="'.$this->naviHref.'?month='.sprintf('%02d',$preMonth).'&ns_year='.$preYear.'">Prev</a>'.
                    '<span class="title">'.date('Y M',strtotime($this->currentYear.'-'.$this->currentMonth.'-1')).'</span>'.
                '<a class="ns-agenda-next" href="'.$this->naviHref.'?month='.sprintf("%02d", $nextMonth).'&ns_year='.$nextYear.'">Next</a>'.
            '</div>';
    }
         
    /**
    * create calendar week labels
    */
    private function _createLabels(){  
                 
        $content='';
         
        foreach($this->dayLabels as $index=>$label){
             
            $content.='<li class="'.($label==6?'end title':'start title').' title">'.$label.'</li>';
 
        }
         
        return $content;
    }
	
	private function _createWeeklyLabels(){
		$content='';
         
        foreach($this->dayLabels as $index=>$label){
             
            $content.='<li>'.$label.'</li>';
 
        }
         
        return $content;
	}
     
     
     
    /**
    * calculate number of weeks in a particular month
    */
    private function _weeksInMonth($month=null,$ns_year=null){
         
        if( null==($ns_year) ) {
            $ns_year =  date("Y",time()); 
        }
         
        if(null==($month)) {
            $month = date("m",time());
        }
         
        // find number of days in this month
        $daysInMonths = $this->_daysInMonth($month,$ns_year);
         
        $numOfweeks = ($daysInMonths%7==0?0:1) + intval($daysInMonths/7);
         
        $monthEndingDay= date('N',strtotime($ns_year.'-'.$month.'-'.$daysInMonths));
         
        $monthStartDay = date('N',strtotime($ns_year.'-'.$month.'-01'));
         
        if($monthEndingDay<$monthStartDay){
             
            $numOfweeks++;
         
        }
         
        return $numOfweeks;
    }
 
    /**
    * calculate number of days in a particular month
    */
    private function _daysInMonth($month=null,$ns_year=null){
         
        if(null==($ns_year))
            $ns_year =  date("Y",time()); 
 
        if(null==($month))
            $month = date("m",time());
             
        return date('t',strtotime($ns_year.'-'.$month.'-01'));
    }
     
}
