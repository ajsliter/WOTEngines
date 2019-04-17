<?php

class Engine { 
    public $id;
    public $Manufacturer;
    public $Model;
    public $CountryOfOrigin;
	public $FuelType;
	public $Horsepower;
	private $noerrors = true;
    private $ManufacturerError = null;
    private $ModelError = null;
    private $CountryOfOriginError = null;
	private $FuelTypeError = null;
	private $HorsepowerError = null;
    private $title = "Engine";
    private $tableName = "Engines";
private $filename = "engines";
    
    function create_record() { // display "create" form
        $this->generate_html_top (1);
        $this->generate_form_group("Manufacturer", $this->ManufacturerError, $this->Manufacturer, "autofocus");
        $this->generate_form_group("Model", $this->ModelError, $this->Model);
        $this->generate_form_group("CountryOfOrigin", $this->CountryOfOriginError, $this->CountryOfOrigin);
		$this->generate_form_group("FuelType", $this->FuelTypeError, $this->FuelType);
		$this->generate_form_group("Horsepower", $this->HorsepowerError, $this->Horsepower);
        $this->generate_html_bottom (1);
    } // end function create_record()
    
    function read_record($id) { // display "read" form
        $this->select_db_record($id);
        $this->generate_html_top(2);
        $this->generate_form_group("Manufacturer", $this->ManufacturerError, $this->Manufacturer, "disabled");
        $this->generate_form_group("Model", $this->ModelError, $this->Model, "disabled");
        $this->generate_form_group("CountryOfOrigin", $this->CountryOfOriginError, $this->CountryOfOrigin, "disabled");
		$this->generate_form_group("FuelType", $this->FuelTypeError, $this->FuelType, "disabled");
		$this->generate_form_group("Horsepower", $this->HorsepowerError, $this->Horsepower, "disabled");
        $this->generate_html_bottom(2);
    } // end function read_record()
    
    function update_record($id) { // display "update" form
        if($this->noerrors) $this->select_db_record($id);
        $this->generate_html_top(3, $id);
        $this->generate_form_group("Manufacturer", $this->ManufacturerError, $this->Manufacturer, "autofocus onfocus='this.select()'");
        $this->generate_form_group("Model", $this->ModelError, $this->Model);
        $this->generate_form_group("CountryOfOrigin", $this->CountryOfOriginError, $this->CountryOfOrigin);
		$this->generate_form_group("FuelType", $this->FuelTypeError, $this->FuelType);
		$this->generate_form_group("Horsepower", $this->HorsepowerError, $this->Horsepower);
        $this->generate_html_bottom(3);
    } // end function update_record()
    
    function delete_record($id) { // display "read" form
        $this->select_db_record($id);
        $this->generate_html_top(4, $id);
        $this->generate_form_group("Manufacturer", $this->ManufacturerError, $this->Manufacturer, "disabled");
        $this->generate_form_group("Model", $this->ModelError, $this->Model, "disabled");
        $this->generate_form_group("CountryOfOrigin", $this->CountryOfOriginError, $this->CountryOfOrigin, "disabled");
		$this->generate_form_group("FuelType", $this->FuelTypeError, $this->FuelType);
		$this->generate_form_group("Horsepower", $this->HorsepowerError, $this->Horsepower);
        $this->generate_html_bottom(4);
    } // end function delete_record()
    
    /*
     * This method inserts one record into the table, 
     * and redirects user to List, IF user input is valid, 
     * OTHERWISE it redirects user back to Create form, with errors
     * - Input: user data from Create form
     * - Processing: INSERT (SQL)
     * - Output: None (This method does not generate HTML code,
     *   it only changes the content of the database)
     * - Precondition: Public variables set (Manufacturer, Model, CountryOfOrigin)
     *   and database connection variables are set in datase.php.
     *   Note that $id will NOT be set because the record 
     *   will be a new record so the SQL database will "auto-number"
     * - Postcondition: New record is added to the database table, 
     *   and user is redirected to the List screen (if no errors), 
     *   or Create form (if errors)
     */
    function insert_db_record () {
        if ($this->fieldsAllValid()) { // validate user input
            // if valid data, insert record into table
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "INSERT INTO `$this->tableName` (Manufacturer,Model,CountryOfOrigin,FuelType,Horsepower) values(?, ?, ?, ?, ?)";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->Manufacturer, $this->Model, $this->CountryOfOrigin, $this->FuelType, $this->Horsepower));
            Database::disconnect();
            header("Location: $this->filename.php"); // go back to "list"
        }
        else {
            // if not valid data, go back to "create" form, with errors
            // Note: error fields are set in fieldsAllValid ()method
            $this->create_record(); 
        }
    } // end function insert_db_record
    
    private function select_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT * FROM `$this->tableName` where id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $data = $q->fetch(PDO::FETCH_ASSOC);
        Database::disconnect();		
        $this->Manufacturer = $data['Manufacturer'];
        $this->Model = $data['Model'];
        $this->CountryOfOrigin = $data['CountryOfOrigin'];
		$this->FuelType = $data['FuelType'];
		$this->Horsepower = $data['Horsepower'];
    } // function select_db_record()
    
    function update_db_record ($id) {
        $this->id = $id;
        if ($this->fieldsAllValid()) {
            $this->noerrors = true;
            $pdo = Database::connect();
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "UPDATE `$this->tableName`  set Manufacturer = ?, Model = ?, CountryOfOrigin = ?, Horsepower = ? WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($this->Manufacturer,$this->Model,$this->CountryOfOrigin, $this->Horsepower,$this->id));
            Database::disconnect();
            header("Location: $this->filename.php");
        }
        else {
            $this->noerrors = false;
            $this->update_record($id);  // go back to "update" form
        }
	}//end function update db record
    
    function delete_db_record($id) {
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "DELETE FROM `$this->tableName` WHERE id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        Database::disconnect();
        header("Location: $this->filename.php");
    } // end function delete_db_record()
    
    private function generate_html_top ($fun, $id=null) {
        switch ($fun) {
            case 1: // create
                $funWord = "Create"; $funNext = "insert_db_record"; 
                break;
            case 2: // read
                $funWord = "Read"; $funNext = "none"; 
                break;
            case 3: // update
                $funWord = "Update"; $funNext = "update_db_record&id=" . $id; 
                break;
            case 4: // delete
                $funWord = "Delete"; $funNext = "delete_db_record&id=" . $id; 
                break;
            default: 
                echo "Error: Invalid function: generate_html_top()"; 
                exit();
                break;
        }
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$funWord a $this->title</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                <style>label {width: 5em;}</style>
                    "; 
        echo "
            </head>";
        echo "
            <body>
                <div class='container'>
                    <div class='span10 offset1'>
                        <p class='row'>
                            <h3>$funWord a $this->title</h3>
                        </p>
                        <form class='form-horizontal' action='$this->filename.php?fun=$funNext' method='post'>                        
                    ";
    } // end function generate_html_top()
    
    private function generate_html_bottom ($fun) {
        switch ($fun) {
            case 1: // create
                $funButton = "<button type='submit' class='btn btn-success'>Create</button>"; 
                break;
            case 2: // read
                $funButton = "";
                break;
            case 3: // update
                $funButton = "<button type='submit' class='btn btn-warning'>Update</button>";
                break;
            case 4: // delete
                $funButton = "<button type='submit' class='btn btn-danger'>Delete</button>"; 
                break;
            default: 
                echo "Error: Invalid function: generate_html_bottom()"; 
                exit();
                break;
        }
        echo " 
                            <div class='form-actions'>
                                $funButton
                                <a class='btn btn-secondary' href='$this->filename.php'>Back</a>
                            </div>
                        </form>
                    </div>

                </div> <!-- /container -->
            </body>
        </html>
                    ";
    } // end function generate_html_bottom()

	 private function generate_form_group ($label, $labelError, $val, $modifier="", $fieldType="text") {
        echo "<div class='form-group";
        echo !empty($labelError) ? ' alert alert-danger ' : '';
        echo "'>";
        echo "<label class='control-label'>$label &nbsp;</label>";
        //echo "<div class='controls'>";
        echo "<input "
            . "name='$label' "
            . "type='$fieldType' "
            . "$modifier "
            . "placeholder='$label' "
            . "value='";
        echo !empty($val) ? $val : '';
        echo "'>";
        if (!empty($labelError)) {
            echo "<span class='help-inline'>";
            echo "&nbsp;&nbsp;" . $labelError;
            echo "</span>";
        }
        //echo "</div>"; // end div: class='controls'
        echo "</div>"; // end div: class='form-group'
    } // end function generate_form_group()
    
    private function fieldsAllValid () {
        $valid = true;
        if (empty($this->Manufacturer)) {
            $this->ManufacturerError = 'Please enter Engine Manufacturer';
            $valid = false;
        }
        if (empty($this->Model)) {
            $this->ModelError = 'Please enter Engine Model';
            $valid = false;
        } 
        if (empty($this->CountryOfOrigin)) {
            $this->CountryOfOriginError = 'Please enter Country of Origin';
            $valid = false;
        }
		if (empty($this->Horsepower)) {
            $this->HorsepowerError = 'Please enter Engine horsepower';
            $valid = false;
        }
        return $valid;
		
    } // end function fieldsAllValid() 
    
    function list_records() {
        echo "<!DOCTYPE html>
        <html>
            <head>
                <title>$this->title" . "s" . "</title>
                    ";
        echo "
                <meta charset='UTF-8'>
                <link href='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/css/bootstrap.min.css' rel='stylesheet'>
                <script src='https://stackpath.bootstrapcdn.com/bootstrap/4.1.2/js/bootstrap.min.js'></script>
                    ";  
        echo "
            </head>
            <body>
                <a href='https://github.com/ajsliter/WOTEngines' target='_blank'>Github</a><br />
                <div class='container'>
                    <p class='row'>
                        <h3>$this->title" . "s" . "</h3>
                    </p>
                    <p>
                        <a href='$this->filename.php?fun=display_create_form' class='btn btn-success'>Create</a>
						<a href='logout.php' class='btn btn-warning'>Logout</a> 
					</p>
                    <div class='row'>
                        <table class='table table-striped table-bordered'>
                            <thead>
                                <tr>
                                    <th>Manufacturer</th>
                                    <th>Model</th>
                                    <th>Country of Origin</th>
                                    <th>Fuel Type</th>
									<th>Horsepower</th>
                                </tr>
                            </thead>
                            <tbody>
                    ";
        $pdo = Database::connect();
        $sql = "SELECT * FROM `$this->tableName` ORDER BY CountryOfOrigin ASC, FuelType ASC";
        foreach ($pdo->query($sql) as $row) {
            echo "<tr>";
            echo "<td>". $row["Manufacturer"] . "</td>";
            echo "<td>". $row["Model"] . "</td>";
            echo "<td>". $row["CountryOfOrigin"] . "</td>";
			echo "<td>". $row["FuelType"] . "</td>";
			echo "<td>". $row["Horsepower"] . "</td>";
            echo "<td width=250>";
            echo "<a class='btn btn-info' href='$this->filename.php?fun=display_read_form&id=".$row["id"]."'>Read</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-warning' href='$this->filename.php?fun=display_update_form&id=".$row["id"]."'>Update</a>";
            echo "&nbsp;";
            echo "<a class='btn btn-danger' href='$this->filename.php?fun=display_delete_form&id=".$row["id"]."'>Delete</a>";
            echo "</td>";
            echo "</tr>";
        }
        Database::disconnect();        
        echo "
                            </tbody>
                        </table>
                    </div>
                </div>
		<a href='https://csis.svsu.edu/~ajsliter/cis355/WOTEngines/img/WOTEngines.png' target='_blank'>UML Class Diagram</a><br />
            </body>

        </html>
                    ";  
    } // end function list_records()
    
} // end class Customer