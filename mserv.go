package main

import (
	"encoding/xml"
	"fmt"
	"io/ioutil"
	"log"
	"net/http"
)

type ListBucketResults struct {
	Name        string
	IsTruncated bool
	Contents    []ListBucketResult
}

type ListBucketResult struct {
	Key  string
	Size int
	Url  string
}

func main() {

	library, err := NewListBucketResults("cherrymusic")
	if err != nil {
		log.Fatal(err)
	}

	for _, item := range library.Contents {
		fmt.Println(item.Key, ":", item.Url)
	}
}

func NewListBucketResults(bucketName string) (*ListBucketResults, error) {
	result := &ListBucketResults{}
	bucketUrl := "http://s3.amazonaws.com/" + bucketName + "/"

	resp, err := http.Get(bucketUrl)
	if err != nil {
		return result, err
	}
	defer resp.Body.Close()

	content, err := ioutil.ReadAll(resp.Body)
	if err != nil {
		return result, err
	}

	err = xml.Unmarshal(content, &result)
	if err != nil {
		return result, err
	}

	for i := 0; i < len(result.Contents); i++ {
		result.Contents[i].Url = bucketUrl + result.Contents[i].Key
	}
	return result, nil
}
