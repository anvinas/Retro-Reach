	<?php
		$conn = new mysqli("localhost", "Retro", "Reach", "COP4331");

		$inData = getRequestInfo();
		
		$searchResults = "";
		$searchCount = 0;

		$conn = new mysqli("localhost", "Retro", "Reach", "COP4331");
		if ($conn->connect_error) 
		{
			returnWithError( $conn->connect_error );
		} 
		else
		{
			$stmt = $conn->prepare("select FirstName, LastName from Contacts where (FirstName like ? OR Lastname like ?) and UserID=?");
			$contactName = "%" . $inData["search"] . "%";
			$stmt->bind_param("sss", $contactName, $contactName, $inData["userId"]);
			$stmt->execute();
			
			$result = $stmt->get_result();
			
			while($row = $result->fetch_assoc())
			{
				if( $searchCount > 0 )
				{
					$searchResults .= ",";
				}
				$searchCount++;
				$searchResults .= '"' . $row["FirstName"] . ' ' . $row["LastName"] . '"';
			}
			
			if( $searchCount == 0 )
			{
				returnWithError( "No Records Found" );
			}
			else
			{
				returnWithInfo( $searchResults );
			}
			
			$stmt->close();
			$conn->close();
		}

		function getRequestInfo()
		{
			return json_decode(file_get_contents('php://input'), true);
		}

		function sendResultInfoAsJson( $obj )
		{
			header('Content-type: application/json');
			echo $obj;
		}
		
		function returnWithError( $err )
		{
			$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
			sendResultInfoAsJson( $retValue );
		}
		
		function returnWithInfo( $searchResults )
		{
			$retValue = '{"results":[' . $searchResults . '],"error":""}';
			sendResultInfoAsJson( $retValue );
		}
		
	?>